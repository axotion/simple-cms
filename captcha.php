<?php
include("main.php");
header('Content-type: image/png');

$code = rand(1000,9999);
$_SESSION['code'] = $code;
$image = imagecreatetruecolor(60,30);
$fg = imagecolorallocate($image,200,200,255);
$noise = imagecolorallocate($image,255,255,255);
imagestring($image,5,10,8,$code,$fg);
for($i=0;$i<200;$i++){
    imagesetpixel($image,rand(0,60),rand(0,30),$noise);
}
imagepng($image);
imagedestroy($image);
?>