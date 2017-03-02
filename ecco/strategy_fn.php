<?php
function get_strategy_type($id)
 {
	$sql = "select wb_id from work_bundle where wb_name = '$id'"; 
	$rs = mysql_query($sql);
	$row = mysql_fetch_array($rs);
	return $row[0]; 
 }
 
 function get_Wb_items($id)
 {
	$sql = "select wb_id,wb_name from work_bundle where wb_subid='$id'";
	//echo $sql;
	return mysql_query($sql);
 }
 
 function List_strategy_types()
 {
	$sql = "select wb_id,wb_name from work_bundle where wb_subid = '0'";
	return mysql_query($sql);
 }
 
 function get_service_type($id)
 {
	$sql = "select id,service_name from service_type where strategy_type='$id'";
	return mysql_query($sql);
 }

 function List_strategy_model($id)
 {
	$sql = "select id,strategy_name from strategy_model where strategy_type='$id'";
	return mysql_query($sql);
 }

?>