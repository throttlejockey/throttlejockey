<?php
define('DIR_IMAGE', '/home1/throttle/oc3.throttlejockey.com/external/image/');
// define('DIR_IMAGE', '/home1/throttle/oc3.throttlejockey.com/image/');
$imageFile = "20TH_10_94_JumpWeb-01.jpeg";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('/home1/throttle/oc3.throttlejockey.com/external/image/image.php');

header ('Content-Type: image/jpeg');


$image = new Image(DIR_IMAGE . $imageFile);
$image->resize(1100, 1100);
// $image->save(DIR_IMAGE . $image_new);

$image->show(DIR_IMAGE . $imageFile);
// $im = @imagecreatetruecolor(120, 20)
//       or die('Cannot Initialize new GD image stream');
// $text_color = imagecolorallocate($im, 233, 14, 91);
// imagestring($im, 1, 5, 5,  'A xxx Text x', $text_color);
// imagepng($im);
// imagedestroy($im);
?>