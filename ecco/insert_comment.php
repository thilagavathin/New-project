<?php
ob_start(); session_start(); include_once('config.php');
$discussion_comments=isset($_POST['discussion_comments'])? $_POST['discussion_comments']:'';
$discussionid=isset($_POST['discussionid'])? $_POST['discussionid']:'';
$userid=$_SESSION['adminlogin'];
$createuser=$_SESSION['displayname'];
if($discussion_comments<>'' && $userid<>'' && $createuser<>'')
{
    $sql_insert ="INSERT INTO community_comments(discussion_id,comments, userid,username,created_date) VALUES(".$discussionid.",'".mysql_real_escape_string($discussion_comments)."','".$userid."','".$createuser."',NOW())";
    $result= @mysql_query($sql_insert);
    if($result==1) echo 'success'; else echo 'failure';
}
else echo 'invalid';
?>