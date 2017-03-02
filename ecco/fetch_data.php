<?php
include("config.php");

if(isset($_POST['get_option']))
{
    $region = $_POST['get_option'];
    $find=mysql_query("select name from agency where region='$region'");
    while($row=mysql_fetch_array($find))
    {
        echo "<option>".$row['name']."</option>";
    }

    exit;
}

?>
