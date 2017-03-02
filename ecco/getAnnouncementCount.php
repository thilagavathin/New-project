<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}


if($_SESSION['userrole']==3 || $_SESSION['userrole']==2){
    //Agency Mapping Query
    $user_agency=array();
    $sql_agency="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
    $sql_agency_row = mysql_query($sql_agency);
    while($agency_row=mysql_fetch_array($sql_agency_row)) {
    $user_agency[]=$agency_row['agency_id'];
    }
    $user_agency=array_unique($user_agency);
    if(count($user_agency)!=0){
       $user_agency_ids=implode(',',$user_agency); 
    }else{
        $user_agency_ids='0';
    }
    
    $where="AND (agency_id=0 OR agency_id IN(".$user_agency_ids."))";
}else{
    $where='';
}
$announce_sql="SELECT * FROM announcements WHERE read_user_id NOT LIKE ('%\"".$_SESSION['adminlogin']."\"%') ".$where."";
$announce_qry = mysql_query($announce_sql);
$announce_count=mysql_num_rows($announce_qry);
if($announce_count!=0){
    echo "<span class='notification'>".$announce_count."</span>";
}else{
	echo "<span class='empty-notification'></span>";
}

