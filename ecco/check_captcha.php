<?php
include_once('securimage-master/securimage.php');

        // Code Validation
if(isset($_POST['captcha_code'])){
    $image = new Securimage();
    if ($image->check($_POST['captcha_code']) == true) {
      $captcha_result=1;
    } else {
      $captcha_result=0;
    } 
 }else{
    $captcha_result=0;
 }       
 echo $captcha_result;       
?>