<?php
//load config
include 'conf.php';
//get image
if (strpos($_GET['file'],$output_extension) == true)
{
	$file = $_GET['file'];
}
else
{
	$file = $_GET['file'].'.'.$output_extension;
}
//display file
if (file_exists($destination_folder.$file))
{
	//set date and time of view
	touch($destination_folder.$file);
	header('Content-Type: image/jpeg');
	header('Accept-Ranges: bytes');
	header('Content-Length: '.filesize($destination_folder.$file));
	readfile($destination_folder.$file);
} 
else
{
	header('Status: 404 Not Found');
	header('Content-Type: image/jpeg');
	header('Accept-Ranges: bytes');
	header('Content-Length: '.filesize('images/'.$lang.'_not_found.jpg'));
	readfile('images/'.$lang.'_not_found.jpg');
}
?>