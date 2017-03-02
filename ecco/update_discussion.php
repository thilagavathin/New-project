<?php
ob_start(); session_start(); include_once('config.php');
$title=isset($_POST['discussion_title'])? $_POST['discussion_title']:'';
$content=isset($_POST['discussion_content'])? $_POST['discussion_content']:'';
$discussion_id=isset($_POST['discussion_id'])? $_POST['discussion_id']:'';
$userid=$_SESSION['adminlogin'];
$createuser=$_SESSION['displayname'];
if($title<>'' && $content<>'' && $userid<>'' && $createuser<>'' && $discussion_id<>'')
{
    $sql_update ="UPDATE community_discussion SET title='".mysql_real_escape_string($title)."',content='".mysql_real_escape_string($content)."' WHERE id=".$discussion_id;
    $result= @mysql_query($sql_update);
    if($result==1) echo 'success'; else echo 'failure';
}
else echo 'invalid';
?>