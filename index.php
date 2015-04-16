<?php
include 'conf.php'; //include config
?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="icon" href="../favicon.ico">
<meta name="robots" content="noindex, nofollow" />
<meta name="robots" content="nosnippet" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="language" content="de" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title><?php echo $site_title;?></title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.form.min.js"></script>

<script type="text/javascript">
$(document).ready(function() { 
	var options = { 
			target: '#output',   // target element(s) to be updated with server response 
			beforeSubmit: beforeSubmit,  // pre-submit callback 
			success: afterSuccess,  // post-submit callback 
			resetForm: true        // reset the form after successful submit 
		}; 
		
	 $('#MyUploadForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// always return false to prevent standard browser submit and page navigation 
			return false; 
		}); 
}); 

function afterSuccess()
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		
		if( !$('#imageInput').val()) //check empty input filed
		{
			$("#output").html("<?php echo $error_no_file;?>");
			return false
		}
		
		var fsize = $('#imageInput')[0].files[0].size; //get file size
		var ftype = $('#imageInput')[0].files[0].type; //get file type
		

		//allow only valid image file types 
		switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg': //case 'image/webp':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> <?php echo $error_filetype_not_supported;?>");
				return false
        }
		
		//Allowed file size is less than $max_file_size
		if(fsize><?php echo $max_file_size*1024*1024;?>) 
		{
			$("#output").html("<?php echo $file_chosen_size;?> <b>"+bytesToSize(fsize) +".</b> <?php echo $error_file_too_big;?>");
			return false
		}
				
		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");  
	}
	else
	{
		//Output error to older browsers that do not support HTML5 File API
		$("#output").html("<?php echo $error_browser_not_supported;?>");
		return false;
	}
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

</script>
<link href="style/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="upload-wrapper">
<div align="center">
<h3><a href=""><?php echo $site_title;?></a></h3>
<?php echo $site_describtion;?>
<br><br>
<form action="processupload.php" method="post" enctype="multipart/form-data" id="MyUploadForm">
<input name="image_file" id="imageInput" type="file" accept="image/jpeg,image/gif,image/png"/>
<br><br>
<input type="submit"  id="submit-btn" value="Upload" />
<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
</form>
<div id="output"></div>
</div>
<div id="foot">
	<p style="text-align: center;">
	<a href="https://www.pix-art.de/impressum" title="Impressum" target="_blank">Impressum</a> | <a href="nutzungsbedingungen.html" title="<?php echo $terms_of_use; ?>" onclick="window.open('nutzungsbedingungen.html', 'nutzungsbedingungen', 'width=500, height=350, scrollbars=yes'); return false;"><?php echo $terms_of_use; ?></a> | <a href="https://www.pix-art.de/kontakt" title="<?php echo $contact; ?>" target="_blank"><?php echo $contact; ?></a>
	<br>
	<?php
	echo 'Version ' . $version . '&nbsp;&nbsp;|&nbsp;&nbsp;';
	echo 'Copyright&nbsp;';
	echo date("Y");
	echo '&nbsp;&nbsp;<a href="http://www.pix-art.de" target="_blank">Pix-Art - Fotos und mehr...</a>';
	echo '&nbsp;&nbsp;'.$all_rights_reserved;
	?>
	</p>
</div>
</div>
</body>
</html>