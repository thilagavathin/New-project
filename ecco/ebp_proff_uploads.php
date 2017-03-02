<?php 
include_once('config.php');
session_start();
if(!isset($_SESSION['adminlogin'])){
header('Location:logout.php'); die;
}
if(isset($_POST['add_upload'])){
    $intervention_query= "SELECT * FROM interventions WHERE id='".$_POST['intervention_id']."' ";
    $intervention_data=mysql_query($intervention_query);
    $intervention_row=mysql_fetch_assoc($intervention_data);
    
     if($intervention_row['uploadfilename']!='') {
      $arrayfoldername=unserialize($intervention_row['uploadfoldername']);
      $arrayfilename=unserialize($intervention_row['uploadfilename']);
      $fileCount = count($arrayfilename);  
      ?>
      <label class="mar_t20">Uploaded Items:</label>
      <ul class="ebp_uploads">
      <?php for($f=0;$f<$fileCount;$f++){   if(!empty($arrayfilename[$f])&&!empty($arrayfilename[$f])){?>
      <li id="upload_item<?php echo $f; ?>"><a class="pad_r10" title="<?php echo $arrayfilename[$f]; ?>" target="_blank" href="<?php echo $site_url; ?>/assets/uploader/php-traditional-server/files/<?php echo $arrayfoldername[$f]; ?>/<?php echo $arrayfilename[$f]; ?>">Item<?php echo $f+1; ?></a><i id="remove_uploads<?php echo $f; ?>" class="fa fa-trash" onclick="remove_uploads(<?php echo $f; ?>,<?php echo $_POST['intervention_id']; ?>)"></i></li> 
      <?php }}?>
      </ul>      
      <?php } 
}
if(isset($_POST['report_uploads'])){
    $intervention_query= "SELECT * FROM interventions WHERE id='".$_POST['intervention_id']."' ";
    $intervention_data=mysql_query($intervention_query);
    $intervention_row=mysql_fetch_assoc($intervention_data);
    
     if($intervention_row['uploadfilename']!='') {
          $arrayfoldername=unserialize($intervention_row['uploadfoldername']);
          $arrayfilename=unserialize($intervention_row['uploadfilename']);
          $arraydates=unserialize($intervention_row['upload_dates']);
          $fileCount = count($arrayfilename);  
          ?>
          <?php for($f=0;$f<$fileCount;$f++){   if(!empty($arrayfilename[$f])&&!empty($arrayfilename[$f])){?>
          
            <li class="col-md-3" id="report_box<?php  echo  $f; ?>">
              <div class="report-box">
                  <span><?php echo date('d M Y',strtotime($arraydates[$f]) ) ; ?> <i class="fa fa-cloud-upload"></i></span>
                  <p><?php $file_name=explode('.',$arrayfilename[$f]) ;echo strtoupper($file_name[0]) ?> <strong><a target="_blank" href="<?php echo $site_url; ?>/assets/uploader/php-traditional-server/files/<?php echo $arrayfoldername[$f]; ?>/<?php  echo  $arrayfilename[$f]; ?>">(View)</a></strong></p>
              </div>
           </li>
          
          <?php }}?>
          <?php }
}
if(isset($_POST['delete_upload'])){
    $uploadfoldername=array();
    $uploadfilename=array();
    $intervention_query= "SELECT * FROM interventions WHERE id='".$_POST['intervention_id']."' ";
    $intervention_data=mysql_query($intervention_query);
    $intervention_row=mysql_fetch_assoc($intervention_data);
    if($intervention_row['uploadfilename']!='') {
      $arrayfoldername=unserialize($intervention_row['uploadfoldername']);
      $arrayfilename=unserialize($intervention_row['uploadfilename']);
      
      
      unset($arrayfoldername[$_POST['item_no']]);
      unset($arrayfilename[$_POST['item_no']]);
      
      $uploadfoldername=serialize($arrayfoldername);
      $uploadfilename=serialize($arrayfilename);
      
      echo $insert_qry= "UPDATE `interventions` SET uploadfoldername='".$uploadfoldername."' ,uploadfilename='".$uploadfilename."' WHERE `id` = '".$_POST['intervention_id']."' ";
    
      $insert=mysql_query($insert_qry);
    }
}
?>