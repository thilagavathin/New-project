<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}

$admin_level='s:1:"2";';
$sql_users="SELECT user_id,name FROM login_users  WHERE user_level like '%".mysql_real_escape_string($admin_level)."%'";
$resource_users = mysql_query($sql_users);
$sql_agency = "SELECT id, user_updated, name FROM agency order by name ";
$result_agency = mysql_query($sql_agency) or die(mysql_error());
?>

<div class="row">

    <div class="col-md-8 col-md-offset-2">
        <form class="form-horizontal ga-form" role="form" autocomplete="off">
            <div class="form-group">
                <label for="name" class="col-sm-5 control-label">Choose User</label>
                <div class="col-sm-7">
                    <select class="form-control" id="spl_user" name="spl_user" data-init-plugin="select2" onchange="check_user_role(this.value);">
                        <option value="" > Select User </option>
                        <?php while($row=mysql_fetch_array($resource_users)) { ?>
                            <option value="<?php echo $row['user_id'];?>" > <?php echo $row['name'];?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-5 control-label">Choose Agency</label>
                <div class="col-sm-7" id="demo">
                    <select name="agency_id" class="select2-drop form-control" data-init-plugin="select2">
                        <?php while($row_agency = mysql_fetch_array($result_agency)) { ?>
                            <option value="<?php echo $row_agency['id']; ?>"  ><?php echo $row_agency['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>


            <div class="text-right m-t-40">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addassign_spl();">Assign</button>
            </div>

        </form>
    </div>

</div>