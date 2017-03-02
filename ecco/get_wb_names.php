<?php 
include_once('config.php');
include_once('strategy_fn.php');
@session_start();
if(!isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}
if(isset($_POST['service_type'])){?>
        <option value="">Select</option>
		<?php	
        $strategy = get_strategy_type($_POST['strategy_type']);
		$service_type = get_service_type($strategy);
		while($type=mysql_fetch_array($service_type)) { ?>
		<option value="<?php echo $type[1]?>"><?php echo $type[1]?></option>
		<?php }    
}else if(isset($_POST['wb_name'])){
$bundle_sql ="SELECT * FROM interventions WHERE id=".$_POST['intervention_id'] ;
$bundle_details = mysql_query($bundle_sql);
$bundle_row = mysql_fetch_assoc($bundle_details);
$work_bundle             =($bundle_row['work_bundle']!='' ? unserialize($bundle_row['work_bundle']): $bundle_row['work_bundle']);
$work_bundle=array_unique($work_bundle);
 ?>
 
 <div class="form-group">
     <label>Filter by Name</label>
     <select class="form-control" id="filter_name" name="filter_name">
        <option value="">All</option>
        <?php for($w=0;$w<count($work_bundle);$w++){?>
        <option value="<?php echo trim($work_bundle[$w]); ?>"><?php echo $work_bundle[$w]; ?></option>
        <?php }?>
     </select>
  </div>
<?php }?>