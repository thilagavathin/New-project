<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$html = "";
$region = trim($_POST["region"]);
$agency_list=mysql_query("SELECT id,name FROM agency  order by name ");
if(!empty($region)){
    $agency_list=mysql_query("SELECT id,name FROM agency WHERE region='".$region."' order by name ");
}
$html .= '<option value="">Select an Agency</option>';
if(mysql_num_rows($agency_list) > 0) {
    while ($row1 = mysql_fetch_array($agency_list)) {
        $html .= ' <option value="' . $row1['id'] . '">' . $row1['name'] . '</option>';
    }
}
echo $html;
die;
