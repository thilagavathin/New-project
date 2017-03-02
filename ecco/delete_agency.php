<?php ob_start(); session_start(); include_once('config.php');
$delete_agency = "DELETE FROM `agency` WHERE `id` = '".$_REQUEST['aid']."'";
$result = mysql_query($delete_agency);
if($result==1) {
    @mysql_query('DELETE FROM agency_map WHERE id='.$_REQUEST['aid']);
    @mysql_query('DELETE FROM TTA_Forms WHERE id='.$_REQUEST['aid']);
    @mysql_query('DELETE FROM tta_progress_notes WHERE id='.$_REQUEST['aid']);
    @mysql_query('DELETE FROM tta_regarding_status WHERE id='.$_REQUEST['aid']);
    @mysql_query('DELETE FROM TTA_Reports_imports WHERE id='.$_REQUEST['aid']);
    @mysql_query('DELETE FROM TTA_Reports_uploads WHERE id='.$_REQUEST['aid']);
    @mysql_query('DELETE FROM TTA_Report_comment WHERE id='.$_REQUEST['aid']);
}
header('Location:agencies.php');
die;
?>