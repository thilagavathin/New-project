<?php ob_start(); session_start(); include_once('config.php');
if(isset($_POST['contract_numGET'])){
	$sql_agency = "SELECT `Id`, `name`, `manager_name`, `phone`, `street`, `apt`, `city`, `state`, `zip` FROM `agency` WHERE id = '".$_POST['agency_id']."'";
	$result_agency = mysql_query($sql_agency) or die(mysql_error()); 
	$row_agency = mysql_fetch_array($result_agency);
	$agencyAddress = $row_agency['street']. " ".$row_agency['apt'].", ".$row_agency['city']. ", ".$row_agency['state'].", ".$row_agency['zip'];
	$update_agency = "UPDATE TTA_Forms
                       SET `agency_id` = '".$_POST['agency_id']."',
                            `assignedUser` = '".$_POST['username']."',
                            `AgencyName` = '".$row_agency['name']."',
                            `ManagerName` = '".$row_agency['manager_name']."',
                            `AgencyContactNumber` = '".$row_agency['phone']."',
                            `AgencyAddress` = '".$agencyAddress."'
                       WHERE `contract_num` = '".$_POST['contract_numGET']."'";
	$result = mysql_query($update_agency);
	if($result==1) {
		?><script>window.location.href = "agency_process.php?contract_num=<?php echo $_POST['contract_numGET']; ?>";</script><?php
		die;
	}else{
        header('Location:message.php');
        die;
	}
}else{

    $get_user_id=mysql_query("SELECT user_id FROM login_users where username ='".$_POST['username']."'");
    $sql_user_id=mysql_fetch_row($get_user_id);
    if (is_array($_POST['agency_id']))
    {
        $ii=0;
        for($i=0;$i<count($_POST['agency_id']);$i++)
        {
           $sno='';$contract_num='';
           $sno= $ii+$_POST['contract_num_rand'];
           $contract_num = $_POST['contract_num'].$sno;
           $tta_check = "SELECT agency_id FROM TTA_Forms WHERE contract_num = '".$contract_num."'";
            $result_tta_check = mysql_query($tta_check) or die(mysql_error());
            $num_rows = mysql_num_rows($result_tta_check);
            $agency_code=$_POST['agency_id'][$i];

            if($num_rows == 0){
                $sql_agency = "SELECT `Id`, `name`, `manager_name`, `phone`, `street`, `apt`, `city`, `state`, `zip` FROM `agency` WHERE id = '".$agency_code."'";
                $result_agency = mysql_query($sql_agency) or die(mysql_error());
                $row_agency = mysql_fetch_array($result_agency);

                $agencyAddress = $row_agency['street']. " ".$row_agency['apt'].", ".$row_agency['city']. ", ".$row_agency['state'].", ".$row_agency['zip'];

                $insert_agency = "INSERT INTO TTA_Forms (
							`agency_id`,
							`contract_num`,
							`created_date`,
							`user_updated`,
							`updated_date`,
							`assignedUser`,
							`AgencyName`,
							`ManagerName`,
							`AgencyContactNumber`,
							`AgencyAddress`,
							`requestedUser`
						)
						VALUES (
							'".$agency_code."',
							'".$contract_num."',
							CURRENT_TIMESTAMP,
							'".$_SESSION['adminlogin1']."',
							CURRENT_TIMESTAMP,
							'".$_POST['username']."',
							'".$row_agency['name']."',
							'".$row_agency['manager_name']."',
							'".$row_agency['phone']."',
							'".$agencyAddress."',
							'".$_SESSION['adminlogin1']."'
						)";
                $result = mysql_query($insert_agency);
                $SQL_HELP ="INSERT INTO help (date_time,agency,contract_num) VALUES(CURRENT_TIMESTAMP,'".$agency_code."','".$contract_num."')";
                mysql_query($SQL_HELP);
                $sql_agency_map=mysql_query("SELECT id FROM agency_map WHERE user_id=".$sql_user_id[0]." AND agency_id=".$agency_code);
                $map_agency=mysql_num_rows($sql_agency_map);
                if($map_agency==0) $insert_query=mysql_query("INSERT INTO agency_map (user_id,agency_id) VALUES (".$sql_user_id[0].",".$agency_code.")");
            }
            $ii++;
        }
            ?><script>window.location.href = "agency_process.php?contract_num=<?php echo $contract_num; ?>";</script><?php
            die;

    }
else
{
    $get_user_id=mysql_query("SELECT user_id FROM login_users where username ='".$_POST['username']."'");
    $sql_user_id=mysql_fetch_row($get_user_id);
    // user Login Delete
    $del_normal="delete FROM TTA_Forms where assignedUser='".$_POST['username']."'";

    mysql_query($del_normal);
    // Insert
    $contract_num = $_POST['contract_num'].$_POST['contract_num_rand'];
    $tta_check = "SELECT * FROM TTA_Forms WHERE `contract_num` = '".$contract_num."'";
    $result_tta_check = mysql_query($tta_check) or die(mysql_error());
    $num_rows = mysql_num_rows($result_tta_check);

    if($num_rows == 0){
        $sql_agency = "SELECT `Id`, `name`, `manager_name`, `phone`, `street`, `apt`, `city`, `state`, `zip` FROM agency WHERE id = '".$_POST['agency_id']."'";
        $result_agency = mysql_query($sql_agency) or die(mysql_error());
        $row_agency = mysql_fetch_array($result_agency);

        $agencyAddress = $row_agency['street']. " ".$row_agency['apt'].", ".$row_agency['city']. ", ".$row_agency['state'].", ".$row_agency['zip'];
        $contract_num = $_POST['contract_num'].$_POST['contract_num_rand'];
       $insert_agency = "INSERT INTO TTA_Forms (
							`agency_id`,
							`contract_num`,
							`created_date`,
							`user_updated`,
							`updated_date`,
							`assignedUser`,
							`AgencyName`,
							`ManagerName`,
							`AgencyContactNumber`,
							`AgencyAddress`,
							`requestedUser`
						)
						VALUES (
							'".$_POST['agency_id']."',
							'".$contract_num."',
							CURRENT_TIMESTAMP,
							'".$_SESSION['adminlogin1']."',
							CURRENT_TIMESTAMP,
							'".$_POST['username']."',
							'".$row_agency['name']."',
							'".$row_agency['manager_name']."',
							'".$row_agency['phone']."',
							'".$agencyAddress."',
							'".$_SESSION['adminlogin1']."'
						)";
        $result = mysql_query($insert_agency);
        $SQL_HELP ="INSERT INTO help (date_time,agency,contract_num) VALUES(CURRENT_TIMESTAMP,'".$_POST['agency_id']."','".$contract_num."')";
        mysql_query($SQL_HELP);
        if($result==1) {
            $sql_agency_map=mysql_query("SELECT id FROM agency_map WHERE user_id=".$sql_user_id[0]." AND agency_id=".$_POST['agency_id']);
            $map_agency=mysql_num_rows($sql_agency_map);
            if($map_agency==0) $insert_query=mysql_query("INSERT INTO agency_map (user_id,agency_id) VALUES (".$sql_user_id[0].",".$_POST['agency_id'].")");
            ?><script>window.location.href = "agency_process.php?contract_num=<?php echo $contract_num; ?>";</script><?php
            die;
        }else {
            header('Location:message.php');
            die;
        }
    }else{
        echo "<lable style='margin-left:450px;' align='center'><h3>Already Register Contract Number<h3/></label>";
    }
}

}
?>