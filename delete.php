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
$file_name = $file;
$thumb_file_name = str_replace ('.'.$output_extension , $thumb_suffix , $file).'.'.$output_extension;
//display file
if (file_exists($destination_folder.$file))
{
	unlink($destination_folder.$file);
	unlink($destination_folder.$thumb_file_name);
	?>
	<script type="text/javascript" language="Javascript">
		alert(unescape('<?php echo $info_deleted; ?>'));
		window.close();
	</script>
	<?php
	echo $info_windows_close;
} 
else
{
	header('Status: 404 Not Found');
	header('Content-Type: image/jpeg');
	readfile('images/'.$lang.'_not_found.jpg');
}
?>