<?php
//load config
include 'conf.php';
// Get image string posted from Android App
$base=$_REQUEST['image'];

if ($base){
	// Decode Image
	$binary=base64_decode($base);
	
	header('Content-Type: image/jpeg');
	
	//create a new filename with actual date/time and random number for new image
	//Imagename: $image_name_only
	$new_file = date('Y-m-d_H-i-s').'_'.randomstring();
	
	//generate filenames without extension
	$thumb_file_name = $new_file.$thumb_suffix;
	$image_file_name = $new_file;
	//folder path to save resized images and thumbnails
	$thumb_save_folder 	= $destination_folder . $thumb_file_name.'.'.$output_extension; 
	$image_save_folder 	= $destination_folder . $image_file_name.'.'.$output_extension;
	
	
	// Images will be saved in folder
	$file = fopen($image_save_folder, 'wb');
	// Create File
	fwrite($file, $binary);
	fclose($file);
	// rework image
	$im = imagecreatefromjpeg ($image_save_folder);
	// interlace image
	imageinterlace($im, true);
	// overwrite with new file
	imagejpeg($im, $image_save_folder);
	
	// Create Thumbs
	$image_size = getimagesize($image_save_folder);
	$image_width = $image_size[0];
	$image_height = $image_size[1];
	$image_type = 'image/jpeg';
	
	normal_resize_image($im, $thumb_save_folder, $image_type, $max_thumb_size, $image_width, $image_height, $jpeg_quality);
	
	// Free up memory
	imagedestroy($im);
	
	
	// We have succesfully created the image
	echo 'https://'.$_SERVER["HTTP_HOST"].'/'.$webpath.'/display/file/'.$image_file_name.'.'.$output_extension;

} else {
	header('Status: 404 Not Found');
	echo "Error";
}


#### Random filename ####
function randomstring($length = 12) {
  // $chars - define all allowed chars
  $chars = "abcdefghijklmnopqrstuvwxyz";
  //$chars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $chars .= "1234567890";
  // Start function
  srand((double)microtime()*1000000);
  $i = 0; // Counter = 0
  while ($i < $length) { // $i < $length
    // take a random char
    $num = rand() % strlen($chars);
    // run substr to fetch a char
    $tmp = substr($chars, $num, 1);
    // add char
    $random = $random.$tmp;
    // $i++ to rise the counter
    $i++;
  }
  return $random;
}

#####  This function will proportionally resize image ##### 
function normal_resize_image($source, $destination, $image_type, $max_size, $image_width, $image_height, $quality){
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
	//do not resize if image is smaller than max size
	if($image_width <= $max_size && $image_height <= $max_size){
		if(save_image($source, $destination, $image_type, $quality)){
			return true;
		}
	}
	//Construct a proportional size of new image
	$image_scale	= min($max_size/$image_width, $max_size/$image_height);
	$new_width		= ceil($image_scale * $image_width);
	$new_height		= ceil($image_scale * $image_height);
	$new_canvas		= imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image
	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
		save_image($new_canvas, $destination, $image_type, $quality); //save resized image
	}
	return true;
}

##### Saves image resource to file ##### 
function save_image($source, $destination, $image_type, $quality){
	switch(strtolower($image_type)){//determine mime type
		case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg': //case 'image/webp': 
    	imageinterlace($source, true);
    	imagejpeg($source, $destination, $quality);
    	return true;
			break;
		default: return false;
	}
}

##### Function to get the client IP address #####
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?>