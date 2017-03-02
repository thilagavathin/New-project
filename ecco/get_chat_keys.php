<?php 
include_once('config.php');
session_start();
if(!isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}
$new_key = array();
$real_keywords=mysql_query("SELECT keywords from chats");
while($keywords=mysql_fetch_array($real_keywords)){
$rest_key = $keywords['keywords'];
if($rest_key!=''){
   $new_key[]=unserialize($rest_key);   
}
 
}
foreach($new_key as $key_words){
    if(is_array($key_words)){
        foreach($key_words as $key_word){
            $post_keys[]=$key_word;
        }
    }else{
        $post_keys[]=$key_words;
    }
}
$post_keys=array_unique($post_keys);
foreach($post_keys as $key) { ?>
<option value="<?php echo $key; ?>"><?php echo $key; ?></option> 
<?php } ?>
                      