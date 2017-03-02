<?php
ob_start(); session_start(); include_once('config.php');
if($_SESSION['userrole']=='' && $_SESSION['adminlogin1']=='') {
    header('Location:dashboard.php'); die;
}
$agency_id=$_POST['agency'];
$user_list=rtrim($_POST['users'],',');

$admin_level='s:1:"1";';

if( empty($user_list)  ){
    $sql_users="SELECT user_id,name FROM login_users  WHERE user_level not like '%".mysql_real_escape_string($admin_level)."%' ORDER BY name ASC";
    $resource_users = mysql_query($sql_users);
}else{
    $sql_users="SELECT user_id,name FROM login_users  WHERE user_id not in (".$user_list.") AND user_level not like '%".mysql_real_escape_string($admin_level)."%' ORDER BY name ASC";
    $resource_users = mysql_query($sql_users);
}
?>
<div class="row">

    <div class="col-md-8 col-md-offset-2">
        <form class="form-horizontal ga-form" role="form" autocomplete="off">
            <div class="form-group">
                <label for="name" class="col-sm-5 control-label">Agency Name</label>
                <div class="col-sm-7">
                    <input type="text" placeholder="" value="<?php echo $_POST['agencyname'];?>" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-5 control-label">Assign to</label>
                <div class="col-sm-7">
                    <div class="m-t-20">
                        <select class="full-width" name="assign_user" id="assign_user" data-init-plugin="select2">
                            <option value="" > Select User </option>
                            <?php while($row=mysql_fetch_array($resource_users)) { ?>
                                <option value="<?php echo $row['user_id'];?>" > <?php echo $row['name'];?> </option>
                            <?php } ?>
                        </select>

                    </div>
                </div>
            </div>



            <div class="text-right m-t-40">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addassign_normal_admin();" class="btn btn-primary">Assign</button>
            </div>

        </form>
    </div>

</div>
