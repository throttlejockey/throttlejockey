<?php
ob_clean();
flush();
$thePath = '/home1/throttle/storage/logs/lists/';
$theFile = 'categories_30.txt';

if( file_exists( $thePath . $theFile )) {
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$theFile.'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . $thePath . $theFile);
    readfile($thePath . $theFile);
} else {
    print "No File :(";
}
exit();

?>