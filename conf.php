<?php
// Version number
$version = '0.11';
# Configuration #########################
$max_thumb_size 		= '250'; //Maximum thumbnails size (height and width)
$max_image_size 		= '1920'; //Maximum image size (height and width)
$thumb_suffix				= '_thumb'; //thumb prefix
$destination_folder	= 'uploads/'; //upload directory ends with / (slash)
$webpath						= 'imagehost'; //path relative to wwwroot witout /
$jpeg_quality 			= '75'; //jpeg quality
$output_extension   = 'jpg'; //jpg
$max_file_size		  = '10'; //in MB
$app_path						= 'app/'; //with trailing slash
$app_file						= 'Pix-Art_imagehost_1.4.apk'; // App File name
$lang_path					= 'lang/'; //with trailing slash
##########################################

# Language ###############################
$site_title         = 'Pix-Art - Image Hosting Service';
// language files in /lang/ directory
// Language detection
$languages = array('de', 'de-DE', 'en', 'en-GB', 'en-US');
$header = array();
$header = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
foreach($header as $lang) {
   if(in_array($lang, $languages)) {
   	include $lang_path.$lang.'.php'; //include language file i.e. fi.php or se.php
     break;
   }
   else {
   include $lang_path.'en.php'; //include english language
   }
}
##########################################

/* Changelog #############################
0.11 - 15.04.2015
================
- added english language in webapp

0.10 - 13.04.2015
================
- bug fixes
- included error notification when viewing all files in browser
- added english language in app

0.9 - 28.03.2015
================
- changed URL to xmpp.pix-art.de (also in apk)

0.8 - 05.03.2015
================
- use only small letters in filename
- reworked imageinterlace

0.7 - 05.02.2015
================
- added  "Content-Lenght" to display script
- changed URL from http to https
- added interlace bit for mobile shared images
- added thumbs for mobile shared images

0.6 - 27.01.2015
================
- added Android app and PHP script to upload and share directly from mobile devices

0.5 - 13.09.2014
================
- change filename into: YYYY-mm-dd_HH-ii-ss_randomchars(12).jpg
- change thumb filename into: YYYY-mm-dd_HH-ii-ss_randomchars(12)_thumb.jpg

0.4 - 09.09.2014
================
- add imageinterlace bit

0.3 - 24.08.2014
================
- rotate images based on exif data
- added link to delete image

0.2 - 22.08.2014
================
- set date/time of last file access

0.1 - 18.08.2014
================
- initial version
- base64 encoded file names (decoded format: YYYY-mm-dd_HH-ii-ss_randomnumber.jpg)
- save images as jpg
- reduce quality and resoloution
*/
?>
