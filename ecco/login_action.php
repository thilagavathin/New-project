<?php
error_reporting(0);
ob_start(); session_start();
include_once('config.php');
$keepme='';
$username = isset($_POST["username"])? $_POST["username"]:'';
$password = isset($_POST["password"])? $_POST["password"]:'';
$keepme= isset($_POST['signedin'])? $_POST["signedin"]:'';
$error = true;

if($username<>'' && $password<>'')
{
    @$sql=mysql_query("SELECT user_id,username,name,user_level,approved FROM login_users where username ='".mysql_real_escape_string($_POST['username'])."' and password ='".mysql_real_escape_string(md5($_POST['password']))."'");
    if(mysql_num_rows($sql)>0)
    {
        $row=mysql_fetch_assoc($sql);
        if($row['approved']=='NO'){
            $error = false;
            echo "Your account is not approved";
        }
        else
        {
            if($keepme ==1)
            {   
                setcookie("username", $username, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
            }else{
                unset($_COOKIE['username']);
                setcookie('username', null, -1, '/');
                setcookie("password", "", time() - 3600);
            }
            @$user_level=unserialize($row['user_level']);
            @$user_role= min($user_level);
            $_SESSION['adminlogin'] = $row['user_id'];
            $_SESSION['adminlogin1'] = $row['username'];
            $_SESSION['displayname'] = $row['name'];
            $_SESSION['userrole'] = $user_role;
            $_SESSION['menu'] = $row['user_id'];
            echo 'success';exit;
        }
    }
    else echo "Invalid User Name / Password";
}
elseif($username<>'' && $password=='')  echo "Password Is Required";
elseif($username=='' && $password<>'') echo "Username Is Required";
else
{
    header('Location:login.php');
    die;
}
?>