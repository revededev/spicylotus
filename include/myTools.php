<?php
// echo 'inside_myTools<br>';


/**
 * @param object $a object or array
 * @return print inside pre balise
 */
function printr($a) { echo '<pre style="color:red;line-height:1.2;margin:0;padding:0;font-size: 14px;">';print_r($a);echo '</pre>';}

/**
 * @param array $myErrors array of string
 * @return print inside p balise
 */
function printMyErrors($myErrors){ // array string
    echo "<div>";
    foreach($myErrors as $myError){
        echo '<p style="color:red;line-height:1.2;font-size: 90%;margin:0;padding:0;">'.$myError.'</p>';
    }
    echo "</div>";
}
/**
 * @param string $text
 * @return print $text inside i balise + br
 */
function printText_i($text){
    echo '<i>'.$text.' </i><br>';
}
/**
 * @param string $text
 * @return print $text inside strong balise + br
 */
function printText_strong($text){
    echo '<strong>'.$text.' </strong><br>';
}
/**
 * @param string $message
 * @return alert script with $message
 */
function function_alert($message) {// Display the alert box  
    echo "<script>alert('".$message."');</script>"; 
} 

// function image_resize($file_name, $width, $height, $crop=FALSE) {
//     list($wid, $ht) = getimagesize($file_name);
//     $r = $wid / $ht;
//     if ($crop) {
//        if ($wid > $ht) {
//           $wid = ceil($wid-($width*abs($r-$width/$height)));
//        } else {
//           $ht = ceil($ht-($ht*abs($r-$width/$height)));
//        }
//        $new_width = $width;
//        $new_height = $height;
//     } else {
//        if ($width/$height > $r) {
//           $new_width = $height*$r;
//           $new_height = $height;
//        } else {
//           $new_height = $width/$r;
//           $new_width = $width;
//        }
//     }
//     $source = imagecreatefromjpeg($file_name);
//     $dst = imagecreatetruecolor($new_width, $new_height);
//     imagecopyresampled($dst, $source, 0, 0, 0, 0, $new_width, $new_height, $wid, $ht);
//     return $dst;
// }
//  $img_to_resize = image_resize(‘path-to-jpg-image’, 250, 250);
  
?>