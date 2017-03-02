<?php ob_start(); session_start(); include_once('config.php');
$sql_agency = "SELECT id, name FROM agency ";
$result_agency = mysql_query($sql_agency) or die(mysql_error());
$get_user="SELECT user_level FROM login_users where username='".$_GET['id']."'";
$result_user = mysql_query($get_user);
$row=mysql_fetch_array($result_user);
$user_level=unserialize($row['user_level']);
$user_role= min($user_level);
?>
<link rel="stylesheet" type="text/css" href="multiselect/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="multiselect/style.css" />
<link rel="stylesheet" type="text/css" href="multiselect/jquery-ui.css" />
<script type="text/javascript" src="multiselect/1.js"></script>
<script type="text/javascript" src="multiselect/2.js"></script>
<script type="text/javascript" src="multiselect/jquery.multiselect.js"></script>
<?php
if($user_role==3)
{?>
    <label>Choose Agency</label>
    <select id="agency_idd" name="agency_id" class="full-width form-control" data-init-plugin="select2" required >
        <?php while($row_agency = mysql_fetch_array($result_agency)) { ?>
            <option value="<?php echo $row_agency['id']; ?>"  ><?php echo $row_agency['name']; ?></option>
        <?php } ?>
    </select>
<?php
}
else
{
?>
    <label>Choose Agency</label>
    <select id="agency_id" class="full-width form-control" data-init-plugin="select2" multiple name="agency_id[]" >
        <?php while($row_agency = mysql_fetch_array($result_agency)) { ?>
            <option selected value="<?php echo $row_agency['id']; ?>"  ><?php echo $row_agency['name']; ?></option>
        <?php } ?>
    </select>
<?php
}
?>
<script type="text/javascript"> $("#agency_id").multiselect(); </script>

