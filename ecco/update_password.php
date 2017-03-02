<?php ob_start(); session_start(); include_once('config.php');
$cur_pass=(isset($_POST['cur_pass']))? $_POST['cur_pass']:'';
$new_pass=(isset($_POST['new_pass']))? $_POST['new_pass']:'';
if($cur_pass<>'' && $new_pass<>'')
{
    $cur_pass_md='';
    $cur_pass_md=md5($cur_pass);
    $sql_cred= mysql_query("SELECT user_id FROM login_users WHERE user_id='".$_SESSION['adminlogin']."' AND password='".$cur_pass_md."'");
    $status=mysql_num_rows($sql_cred);
    if($status==1)
    {
        $new_pass_md=md5($new_pass);
        $sql_update="UPDATE login_users SET password='".$new_pass_md."',timestamp='".date('Y-m-d H:i:s')."' WHERE user_id='".$_SESSION['adminlogin']."' ";
        if(mysql_query($sql_update)==1) echo 'success'; else echo 'failure';
    }
    else echo 'invalid';
}
else echo 'invalid';
?>