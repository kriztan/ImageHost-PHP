<?php
//load config
include 'conf.php';
//continue only if $_POST is set and it is a Ajax request
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

	// check $_FILES['ImageFile'] not empty
	if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
			die($error_no_file); // output error when above checks fail.
	}
	
	//rotate the image based on EXIF data
	//collect image data for rotation
	$image_temp = $_FILES['image_file']['tmp_name']; //file temp
	$image_size_info 	= getimagesize($image_temp); //get image size
	$image_type 		= $image_size_info['mime']; //image type
	
	switch($image_type){
		case 'image/jpeg': case 'image/pjpeg':
			correctImageOrientation($_FILES['image_file']['tmp_name']);
		break;
		default:
  		$_FILES['image_file']['tmp_name'];
  	break;
	}
	
	//uploaded file info we need to proceed
	$image_name = $_FILES['image_file']['name']; //file name
	$image_size = $_FILES['image_file']['size']; //file size
	$image_temp = $_FILES['image_file']['tmp_name']; //file temp
	$image_size_info 	= getimagesize($image_temp); //get image size
	
	if($image_size_info){
		$image_width 		= $image_size_info[0]; //image width
		$image_height 	= $image_size_info[1]; //image height
		$image_type 		= $image_size_info['mime']; //image type
	}else{
		die($error_filetype_not_supported);
	}
	//switch statement below checks allowed image type 
	//as well as creates new image from given file 
	switch($image_type){
		case 'image/png':
			$image_res = imagecreatefrompng($image_temp); 
			break;
		case 'image/gif':
			$image_res = imagecreatefromgif($image_temp); 
			break;			
		case 'image/jpeg': case 'image/pjpeg':
			$image_res = imagecreatefromjpeg($image_temp); 
			break;
		/*case 'image/webp':
			$image_res = imagecreatefromwebp($image_temp); 
		break;*/
		default:
			$image_res = false;
	}

	if($image_res){
		//Get file extension and name to construct new file name 
		$image_info = pathinfo($image_name);
		$image_extension = strtolower($image_info["extension"]); //image extension
		$image_name_only = strtolower($image_info["filename"]);//file name only, no extension
		
		//create a new filename with actual date/time and random number for new image
		//Imagename: $image_name_only
		$new_file = date('Y-m-d_H-i-s').'_'.randomstring();
		
		//generate filenames without extension
		$thumb_file_name = $new_file.$thumb_suffix;
		$image_file_name = $new_file;
		//folder path to save resized images and thumbnails
		$thumb_save_folder 	= $destination_folder . $thumb_file_name.'.'.$output_extension; 
		$image_save_folder 	= $destination_folder . $image_file_name.'.'.$output_extension;
		
		//call normal_resize_image() function to proportionally resize image
		if(normal_resize_image($image_res, $image_save_folder, $image_type, $max_image_size, $image_width, $image_height, $jpeg_quality))
		{
			//call crop_image_square() function to create square thumbnails
			if(!normal_resize_image($image_res, $thumb_save_folder, $image_type, $max_thumb_size, $image_width, $image_height, $jpeg_quality))
			{
				die($error_no_thumb);
			}

			/* We have succesfully resized and created thumbnail image
			We can now output image to user's browser or store information in the database*/
			echo '<div align="center"><br>';
			echo '<h4>'.$info_preview.'</h4>';
			//display image via php
			//echo '<a href="display.php?file='.$image_file_name.'.'.$output_extension.'" target="_blank"><img src="display.php?file='.$thumb_file_name.'.'.$output_extension.'" alt="Thumbnail"></a>';
			//display image as image
			echo '<a href="display/file/'.$image_file_name.'.'.$output_extension.'" target="_blank"><img src="display/file/'.$thumb_file_name.'.'.$output_extension.'" alt="Thumbnail"></a>';
			echo '<br>';
			echo '<h4>'.$info_link_to_image.'</h4><input id="link" type="text" readonly="readonly" onclick="this.select()" title="'.$info_select_and_copy_link.'" value="https://'.$_SERVER["HTTP_HOST"].'/'.$webpath.'/display/file/'.$image_file_name.'.'.$output_extension.'">';
			echo '<br>';
			echo '<h4>'.$info_link_to_delete_image.'</h4><input id="link" type="text" readonly="readonly" onclick="this.select()" title="'.$info_select_and_copy_link.'" value="https://'.$_SERVER["HTTP_HOST"].'/'.$webpath.'/delete/file/'.$image_file_name.'.'.$output_extension.'">';
			echo '</div>';
		}
		imagedestroy($image_res); //freeup memory
	}
} else {
	header('Status: 404 Not Found');
	echo "Error";
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

##### Rotate image #####
function correctImageOrientation($filename) {
  if (function_exists('exif_read_data')) {
    $exif = exif_read_data($filename);
    if($exif && isset($exif['Orientation'])) {
      $orientation = $exif['Orientation'];
      if($orientation != 1){
        $img = imagecreatefromjpeg($filename);
        $deg = 0;
        switch ($orientation) {
          case 3:
            $deg = 180;
            break;
          case 6:
            $deg = 270;
            break;
          case 8:
            $deg = 90;
            break;
        }
        if ($deg) {
          $img = imagerotate($img, $deg, 0);        
        }
        // then rewrite the rotated image back to the disk as $filename 
        imagejpeg($img, $filename, 95);
      } // if there is some rotation necessary
    } // if have the exif orientation info
  } // if function exists      
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
