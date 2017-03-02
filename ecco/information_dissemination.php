<?php 
include_once('config.php');
include_once('strategy_fn.php');
error_reporting(0);
if(isset($_POST['intervention_id'])){ 
    
   // $types_of_participants=array();
   // $target_population=array();
	
    $in_comm_mem=array();
	
    $intervention_query= "SELECT * FROM interventions WHERE id='".$_POST['intervention_id']."' ";
    $intervention_data=mysql_query($intervention_query);
    $intervention_row=mysql_fetch_assoc($intervention_data);
    if($intervention_row['community']!=''){
       $communities=unserialize($intervention_row['community']); 
       $community_count=count($communities['name']);
    }else{
       $communities=$intervention_row['community']; 
       $community_count=0;
    }
    
    $add_community=($intervention_row['part_a_save']==0 ? 'add_community()':'');
    $rp_cf=($intervention_row['part_b_save']==0 ? 'add_rps()':'');
    
    $PartA=($intervention_row['part_a_save']==0 ? '':'disabled_input');
    $PartB=($intervention_row['part_b_save']==0 ? '':'disabled_input');
    $PartC=($intervention_row['part_c_save']==0 ? '':'disabled_input');
    $PartD=($intervention_row['part_d_save']==0 ? '':'disabled_input');
    $PartE=($intervention_row['part_e_save']==0 ? '':'disabled_input');   
    
    $intervention_community=$intervention_row['intervention_name'].' - '.$intervention_row['intervention_community_name'];     
    $strategy_type=(isset($_POST['strategy_type']) ? $_POST['strategy_type']:$intervention_row['strategy_type']);
    
   // $types_of_participants= ($intervention_row['types_of_participants']!=''?unserialize($intervention_row['types_of_participants']):array());
   // $target_population= ($intervention_row['target_population']!=''?unserialize($intervention_row['target_population']):array());
   
	$in_comm_mem= ($intervention_row['in_comm_mem']!=''?unserialize($intervention_row['in_comm_mem']):array());
	
}
 ?>
   	
<div class="panel-group strategy-collapse" id="accordion">
<!-- Part A Start -->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
        <i class="fa fa-angle-down" aria-hidden="true"></i>Part A</a><span>- Provide Starting Information</span>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse <?php if(!isset($_POST['strategy_type'])){echo ' in ';} echo $PartA; ?>">
      <div class="panel-body">
      <form id="partAForm" >
      <input type="hidden" name="agency_id" value="<?php echo $_POST['agency_id']; ?>" />
      <input value="<?php echo $intervention_row['intervention_name'] ?>" type="hidden" name="intervention_name">
	  <input value="<?php echo $intervention_row['intervention_community_name'] ?>" type="hidden" name="intervention_community_name">
      <input value="<?php echo $_POST['intervention_id'] ?>" type="hidden" name="intervention_id">  
        <div class="col-md-4 portfolio-title-text">    
            <label>Fillers Name</label>
            <input value="<?php echo $intervention_row['fillers_name'] ?>" class="form-control" type="text" name="fillers_name" id="fillers_name"/>
        </div>
        <div class="col-md-4 portfolio-title-text">    
            <label>Email</label>
            <input value="<?php echo $intervention_row['fillers_email'] ?>" class="form-control" type="text" name="fillers_email" id="fillers_email"/>
        </div>
        <div class="col-md-4 portfolio-title-text">    
            <label>Phone</label>
            <input value="<?php echo $intervention_row['fillers_phoneno'] ?>" class="form-control" type="text" name="fillers_phoneno" id="fillers_phoneno"/>
        </div>
        <div class="col-xs-12">
           <span class="separator"></span>                                
        </div>
        <div class="col-md-6">
           <div class="form-group">
              <label class="mar0 checkbox_normal">
                <input <?php if($intervention_row['PC_fillout']=='on'){ echo 'checked=""';} ?>  type="checkbox" id="PC_fillout" name="PC_fillout">
                <span class="custom-icon checkbox-icon"></span>Did PC fill out this form?
              </label>
              <p class="inner-title mar_t20">Contract Communities / ZIP</p>
           </div>
        </div>
        <div class="col-md-6 text-right">
          <div class="custom-fileupload">
            <label id="add_community" onclick="<?php echo $add_community; ?>">
              <i class="fa fa-plus-circle"></i> Add Communities
            </label>
            <input type="hidden" id="community_count" value="<?php echo ($community_count > 0 ? $community_count:1); ?>" />
          </div>
        </div>
        <div class="col-md-12">
            <div class="row community_cover_box">
            <?php 
            if($community_count>0){
            for($c=0;$c<$community_count;$c++){ ?>
                <div class="col-md-4">
                    <div class="communities-box">
                       <h3><span><?php echo $c+1;?></span>
                         <div class="form-group col-md-7 col-xs-7 col-sm-7 col-sm-offset-1 col-xs-offset-1 col-md-offset-1 mar_b0">
                            <input value="<?php echo $communities['name'][$c]; ?>" type="text" class="form-control" name="community[name][]" id="community_name1" placeholder="Community Name">
                         </div> 
                         <div class="form-group col-md-4 col-xs-4 col-sm-4 mar_b0">
                            <input value="<?php echo $communities['zip'][$c]; ?>" type="text" class="form-control" name="community[zip][]" id="community_zip1" placeholder="Zip">
                         </div> 
                       </h3>
                       <div class="communities-box-info">
                          <p>Community Readiness Assessment Date &amp; Score</p>
                          <div class="row">
                              <div class="form-group col-md-6 col-xs-6">
                                  <label class="">Date</label> 
                                  <div class="input-group date form_date" data-date-format="mm/dd/yyyy">
                                    <input class="form-control" type="text" value="<?php echo $communities['date'][$c]; ?>" name="community[date][]" readonly>
                					<span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span>
                                  </div>
                              </div> 
                              <div class="form-group col-md-6 col-xs-6">
                                  <label>Score</label>
                                 <select class="form-control" name="community[score][]">
                                    <option value="">Select</option>
                                    <option <?php if($communities['score'][$c]=='1.No Awareness'){   echo 'selected="selected"';} ?>>1.No Awareness</option>
                                    <option <?php if($communities['score'][$c]=='2.Denial of Data'){ echo 'selected="selected"';} ?>>2.Denial of Data</option>
                                    <option <?php if($communities['score'][$c]=='3.Vague Awareness'){echo 'selected="selected"';} ?>>3.Vague Awareness</option>
                                    <option <?php if($communities['score'][$c]=='4.Preplanning'){    echo 'selected="selected"';} ?>>4.Preplanning</option>
                                    <option <?php if($communities['score'][$c]=='5.Preparation'){    echo 'selected="selected"';} ?>>5.Preparation</option>
                                    <option <?php if($communities['score'][$c]=='6.Initiation'){     echo 'selected="selected"';} ?>>6.Initiation</option>
                                    <option <?php if($communities['score'][$c]=='7.Stabilization'){  echo 'selected="selected"';} ?>>7.Stabilization</option>
                                    <option <?php if($communities['score'][$c]=='8.Confirmation'){   echo 'selected="selected"';} ?>>8.Confirmation</option>
                                    <option <?php if($communities['score'][$c]=='9.Ownership'){      echo 'selected="selected"';} ?>>9.Ownership</option>
                                     
								</select>	
                                		
                              </div>
                          </div>
                       </div>
                    </div>
                </div>
          <?php } }else{?>
                <div class="col-md-4">
                    <div class="communities-box">
                       <h3><span>1</span>
                         <div class="form-group col-md-7 col-xs-7 col-sm-7 col-sm-offset-1 col-xs-offset-1 col-md-offset-1 mar_b0">
                            <input type="text" class="form-control" name="community[name][]" placeholder="Community Name">
                         </div> 
                         <div class="form-group col-md-4 col-xs-4 col-sm-4 mar_b0">
                            <input type="text" class="form-control" name="community[zip][]" placeholder="Zip">
                         </div> 
                       </h3>
                       <div class="communities-box-info">
                          <p>Community Readiness Assessment Date &amp; Score</p>
                          <div class="row">
                              <div class="form-group col-md-6 col-xs-6">
                                  <label class="">Date</label> 
                                  
                                  <div class="input-group date form_date" data-date-format="mm/dd/yyyy">
                                    <input class="form-control" type="text" name="community[date][]" readonly>
                					<span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                              </div> 
                              <div class="form-group col-md-6 col-xs-6">
                                  <label>Score</label>
                                <select class="form-control" name="community[score][]">
                                    <option value="">Select</option>
                                    <option >1.No Awareness</option>
                                    <option >2.Denial of Data</option>
                                    <option >3.Vague Awareness</option>
                                    <option >4.Preplanning</option>
                                    <option >5.Preparation</option>
                                    <option >6.Initiation</option>
                                    <option >7.Stabilization</option>
                                    <option >8.Confirmation</option>
                                    <option >9.Ownership</option>
                                     
								</select>			
                              </div>
                          </div>
                       </div>
                    </div>
                </div>
          <?php }?>      
            </div>
        </div>
        <div class="col-xs-12">
           <span class="separator"></span>
        </div>
        <div class="col-md-4 portfolio-title-text">    
            <label>State Level Manager</label>
            <input value="<?php echo $intervention_row['manager_name'] ?>" class="form-control" type="text" name="manager_name" id="manager_name">
        </div>
        <div class="col-md-4 portfolio-title-text">    
            <label>Email</label>
            <input value="<?php echo $intervention_row['manager_email'] ?>" class="form-control" type="text" name="manager_email" id="manager_email">
        </div>
        <div class="col-md-4 portfolio-title-text">    
            <label>Phone</label>
            <input value="<?php echo $intervention_row['manager_phoneno'] ?>" class="form-control" type="text" name="manager_phoneno" id="manager_phoneno">
        </div>
        <div class="col-xs-12 col-sm-12 text-center form mar_tb30">
            
            <?php if($_POST['intervention_id']>0){                
                $save_btn=($intervention_row['part_a_save']==0 ? 'savePartA()':'not_save()');                 
            ?>
              <input type="hidden" name="partA_edit" value="<?php echo $_POST['intervention_id'] ?>" />   
            <?php }else{
              $save_btn='savePartA()'; 
            ?>
              <input type="hidden" name="partA_save" value="1" />               
            <?php }?>
              <a class="mar_r15 button save_btn" onclick="<?php echo $save_btn; ?>" style="padding: 10px 20px;cursor: pointer;">Save</a>
              <a class="mar_l15 button cancel_btn" onclick="editPart(1)" style="padding: 10px 20px;cursor: pointer;">Edit</a>
         </div>
      </form>
      </div>
    </div>
  </div><!-- Part A End -->
  <?php
  $adddr = isset($_POST['addr'])?$_POST['addr']:$intervention_row['addressed_issue'];
  $appstat = isset($_POST['appstat'])?$_POST['appstat']:$intervention_row['approved_state_priority'];  
  ?>
  <!-- Part B Start -->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title" id="activate_slider">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
        <i class="fa fa-angle-down" aria-hidden="true"></i>Part B</a><span>- Classify The Intervention</span>
      </h4>
    </div>
    <div id="collapse3" class="panel-collapse collapse <?php if(isset($_POST['strategy_type'])){echo ' in ';}else{echo $PartB;}?>">
      <div class="panel-body">
	  <div class="loading text-center" id="loader" style="display:none">
						<img src="<?php echo $site_url; ?>/assets/images/loading.gif">
					</div>
        <form id="partBForm">
        <div class="col-md-12 form pad0">
            <div class="form-group col-md-12">
                <label class="add_button ele_bl text_white pad10 mar0 "><span class="bold">1.</span><?php echo $intervention_community; ?></label>
             </div>
		<div class="col-md-6">
		<div class="form">
			
            <div class="form-group">
              <label><span class="text_blue bold">2. </span>Issue or Problem to be Addressed</label>
              <textarea class="form-control" name="addressed_issue" id="addressed_issue"><?php echo $adddr; ?></textarea>
            </div>
             <div class="form-group">
              <label><span class="text_blue bold">3. </span>Approved State Priority</label>
              <input type="text" class="form-control" name="approved_state_priority" id="approved_state_priority" value="<?php echo $appstat; ?>">
            </div>
              <div class="form-group">
                <label><span class="text_blue bold">4. </span>Strategy Type</label>
                
                <select class="form-control mar_b20" name="strategy_type" id="strategy_type" onchange="change_servicetype(this.value,'<?php echo $_POST['intervention_id']; ?>','<?php echo $intervention_row['strategy_type'];?>','3')">
    				<option value="">Select Strategy Type</option>
    				<?php   
					$strategy = List_strategy_types();						
					while($list = mysql_fetch_array($strategy)) { ?>
					<option <?php if($strategy_type==$list[1]){echo 'selected=""';} ?> value="<?php echo $list[1]?>"><?php echo $list[1]?></option>
					<?php }	?>
                </select>
              </div>
              <div class="form-group">
                <label class=""><span class="text_blue bold">5. </span>IOM Category</label>
                <select class="form-control" name="IOM_category" id="IOM_category">
					<option <?php if($intervention_row['IOM_category']==''){echo 'selected=""';} ?> value="">--Select IOM--</option>
					<option <?php if($intervention_row['IOM_category']=='Universal Direct'){echo 'selected=""';} ?> value="Universal Direct">Universal Direct</option>
                    <option <?php if($intervention_row['IOM_category']=='Universal Indirect'){echo 'selected=""';} ?> value="Universal Indirect">Universal Indirect</option>
					<option <?php if($intervention_row['IOM_category']=='Selected'){echo 'selected=""';} ?> value="Selected">Selected</option>
					<option <?php if($intervention_row['IOM_category']=='Indicated'){echo 'selected=""';} ?> value="Indicated">Indicated</option>
				</select>
              </div>
			</div>
			</div>

			<div class="col-md-6">
			 <div class="form">              
              <div class="form-group">
                <label><span class="text_blue bold">6. </span>Service Type</label>
                <select class="form-control" name="service_type" id="service_type">
                    <option value="">Select</option>
                    <?php	
                    $strategy = get_strategy_type($strategy_type);
					$service_type = get_service_type($strategy);
					while($type=mysql_fetch_array($service_type)) { ?>
					<option <?php if($intervention_row['service_type']==$type[1]){echo 'selected="selected"';} ?> value="<?php echo $type[1]?>"><?php echo $type[1]?></option>
					<?php } ?>
                </select>
              </div>
            <div class="form-group">
            <label><span class="text_blue bold">7. </span>Risk &amp; Protective / Contributing Factors</label>
            
            <div class="cf_box pull-right">
            <div class="rps_cover_box"> 
            
                <?php 
                $inter_cf_count=1;
                $c=1;
                if($intervention_row['IVs']!=''){ 
                    $cfs=unserialize($intervention_row['IVs']);
                    $inter_cf_count=count($cfs['cf'] );
                    
                for($iv=0;$iv<$inter_cf_count;$iv++){ 
                ?>
                <div class="inter_IV<?php echo $c; ?>">
                <div class="form-group col-md-6">
                    <label>Risk &amp; Protective</label>
                    <select class="form-control fw_selectbox" name="IVs[context][]" value="<?php echo $cfs['context'][$iv] ?>">
                    <option></option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <div class="form-group cfs_cover<?php echo $c; ?>">
                    <label>Contributing Factors</label>
                    <?php for($a=0;$a<count($cfs['cf'][$iv]);$a++) {?>
                    <div class="form-group" >
                    <input type="text" class="form-control" name="IVs[cf][<?php echo $iv; ?>][]" value="<?php echo $cfs['cf'][$iv][$a] ?>">
                    </div>
                    <?php }?>
                    </div>
                    
                    <div class="">
                        <button type="button" class="add_button pull-right add_cfs" onclick="add_cfs(<?php echo $c; ?>)"><i class="fa fa-plus-circle" aria-hidden="true"></i>CF</button>
                        
                        <input type="hidden" id="cfs_count<?php echo $c; ?>" value="<?php echo $inter_cf_count; ?>" />
                    </div>
                </div>
                </div>
                <?php $c++;} }else{ 
                ?>
                <div class="inter_IV<?php echo $c; ?>">
                <div class="form-group col-md-6">
                    <label>Risk &amp; Protective</label>
                    <select class="form-control fw_selectbox" name="IVs[context][]">
                    <option></option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <div class="form-group cfs_cover<?php echo $c; ?>">
                    <label>Contributing Factors</label>
                    <div class="form-group" >
                    <input type="text" class="form-control" name="IVs[cf][0][]">
                    </div>
                    </div>
                    
                    <div class="">
                        <button type="button" class="add_button mar_b15 pull-right add_cfs" onclick="add_cfs(<?php echo $c; ?>)"><i class="fa fa-plus-circle" aria-hidden="true"></i>CF</button>
                        <input type="hidden" id="cfs_count<?php echo $c; ?>" value="<?php echo $inter_cf_count; ?>" />
                    </div>
                </div>
                </div>
                <?php  }?>
               
             </div>
              <div class="form-group col-md-8 pull-right">
                <label class="add_button pull-right" id="add_rps" onclick="<?php echo $rp_cf; ?>"><i class="fa fa-plus-circle" aria-hidden="true"></i>Risk &amp; Protective Factors</label>
                <input type="hidden" id="rps_count" value="<?php echo $inter_cf_count; ?>" />
              </div>
             </div>
             
            </div>           
          </div>
        </div>
		</div>
        <div class="col-md-12 form text-center mar_tb30"> 
            <?php if($_POST['intervention_id']>0){
                 $pb = isset($_POST['pb'])?$_POST['pb']:'';
                $saveB=(($intervention_row['part_b_save']==0 || $pb=='y') ? 'savePartB()':'not_save()');      
            ?>
              <input type="hidden" name="partB_edit" value="<?php echo $_POST['intervention_id'] ?>" />   
            <?php }else{
                $saveB='savePartB()';
            ?>
              <input type="hidden" name="partB_save" value="1" />               
            <?php }?>
              <a class="mar_r15 button save_btn" onclick="<?php echo $saveB; ?>" style="padding: 10px 20px;cursor: pointer;">Save</a>
              <a class="mar_l15 button cancel_btn" onclick="editPart(3)" style="padding: 10px 20px;cursor: pointer;">Edit</a>
        </div>
        </form>
      </div>
    </div>
  </div><!-------Part B End---------->
  <!-------Part C Start---------->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" id="partc" <?php if($strategy_type!='' && $strategy_type==$intervention_row['strategy_type']) { echo 'href="#collapse2"'; } else { echo " onclick='alert_error_msg()'"; }?>><!-- href="#collapse2" -->
        <i class="fa fa-angle-down" aria-hidden="true"></i>Part C</a><span>- Store Critical Information</span>
      </h4>
    </div>
    <div id="collapse2" class="panel-collapse collapse">
      <div class="panel-body">
         			  
        <div class="col-md-12">
        <div class="col-md-3 col-sm-4 col-xs-12">
            <label class="label-title" style="margin-top: 0px;">Report Archive<span class="info-badge text-lowercase" onclick="info_taggle()" data-toggle ="tooltip" data-placement ="right" title="Click to view Info">i</span></label>
			</div>
			
			<div class="col-md-9 col-sm-8 col-xs-12 info_taggle" style="display: none;">
				  <div class="custom-blockquote mar_b20">
					<p class="mar0">Post your current Needs Assessment, Capacity, Planning, Logic Models and Evaluation Plans on this page.</p>
				  </div>
			  </div></div>
			<div class="col-md-12">
            <ul class="row" id="report_archives">
			
            <?php if($intervention_row['uploadfilename']!='') {
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
          <?php }else{ echo '<li  class="col-md-3">No Reports</li>';} ?>   
          </ul>         
          
         <span id="item_uploader_partC" class="col-xs-12 col-sm-12 row mar_b20" style="<?php echo ($PartC!='' ? 'display:none' : '');  ?>">         
         <label class="label-title">Upload supporting documentation</label>
         <div id="fine-uploader-manual-trigger1"></div>
            <script type="text/template" id="qq-template-manual-trigger1">
                <div class="qq-uploader-selector qq-uploader"  qq-drop-area-text="Drop files here">
                    <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                    </div>
                    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                        <span class="qq-upload-drop-area-text-selector"></span>
                    </div>
                    <div class="buttons">
                        <div class="qq-upload-button-selector qq-upload-button">
                            <div><i class="fa fa-x fa-cloud-upload" style="padding-right: 10px;"></i>Upload</div>
                        </div>
                    </div>
                    <span class="qq-drop-processing-selector qq-drop-processing">
                        <span>Processing dropped files...</span>
                        <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                    </span>
                    <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                        <li>
                            <div class="qq-progress-bar-container-selector">
                                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                            </div>
                            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                            <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                            <span class="qq-upload-file-selector qq-upload-file"></span>
                            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                            <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                            <span class="qq-upload-size-selector qq-upload-size"></span>
                            <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                            <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                            <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                            <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                        </li>
                    </ul>

                    <dialog class="qq-alert-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <div class="qq-dialog-buttons">
                            <button type="button" class="qq-cancel-button-selector">Close</button>
                        </div>
                    </dialog>

                    <dialog class="qq-confirm-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <div class="qq-dialog-buttons">
                            <button type="button" class="qq-cancel-button-selector">No</button>
                            <button type="button" class="qq-ok-button-selector">Yes</button>
                        </div>
                    </dialog>

                    <dialog class="qq-prompt-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <input type="text">
                        <div class="qq-dialog-buttons">
                            <button type="button" class="qq-cancel-button-selector">Cancel</button>
                            <button type="button" class="qq-ok-button-selector">Ok</button>
                        </div>
                    </dialog>
                </div>
            </script>
        </span>
          
            
        </div>
        <div class="col-xs-12 col-sm-12 text-center form mar_b30">
            <?php if($_POST['intervention_id']>0){
                $saveC=($intervention_row['part_c_save']==0 ? 'savePartC()':'not_save()');    
            ?>
              <input type="hidden" id="partC_edit" name="partC_edit" value="<?php echo $_POST['intervention_id'] ?>" />   
            <?php }else{
                $saveC='savePartC()';
            ?>
              <input type="hidden" name="partC_save" value="1" />               
            <?php }?>
            <a class="mar_r15 button save_btn" onclick="<?php echo $saveC; ?>" style="padding: 10px 20px;cursor: pointer;">Save</a>
            <a class="mar_l15 button cancel_btn" onclick="editPart(2)" style="padding: 10px 20px;cursor: pointer;">Edit</a>
            
            <span class="text_blue_lighten pad_lr20 ft_18">(Help)</span>
         </div>
      </div>
    </div>
  </div>
  <!-------Part C End---------->
  <!-------Part D Start---------->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" id="partd" <?php if($strategy_type!='' && $strategy_type==$intervention_row['strategy_type']) { echo 'href="#collapse4"'; } else { echo " onclick='alert_error_msg()'"; }?>><!-- href="#collapse4" -->
        <i class="fa fa-angle-down" aria-hidden="true"></i>Part D</a><span>- Plan Intervention Scope</span>
      </h4>
    </div>
    <div id="collapse4" class="panel-collapse collapse <?php echo $PartD; ?>">
      <div class="panel-body">
      <form id="partDForm" multipart="" enctype="multipart/form-data">
            <div class="form-group col-md-12">
              <label class="add_button ele_bl text_white pad10 mar0"><span class="bold">1. </span><?php echo $intervention_community; ?></label>
			</div>
        <div class="col-md-6">
            <div class="form">                
                <div class="form-group">
                  <label class="ele_bl"><span class="text_blue bold">2. </span>Is this an evidence-based program (EBP) ?</label>
                  <label class="checkbox_normal mar_r15">
                    <input type="radio" name="EBP" onchange="hide_show('EBP','EBP_cover')" value="Yes" <?php if($intervention_row['EBP']=='Yes'){echo 'checked="checked"';} ?>>
                    <span class="custom-icon radio-icon"></span>Yes
                  </label>
                  <label class="checkbox_normal">
                    <input type="radio" name="EBP" onchange="hide_show('EBP','EBP_cover')" value="No" <?php if($intervention_row['EBP']=='No'){echo 'checked="checked"';} ?>>
                    <span class="custom-icon radio-icon"></span>No
                  </label>                  
                </div>
				<div class="form-group" style="<?php if($intervention_row['EBP']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="EBP_cover">
                <div class="form-group">
                  <label><span class="text_blue bold">3. </span>Strategy Model / Intervention Name</label>                  
                  <select class="form-control strategy_select" id="strategy_model" name="strategy_model" onchange="toogle_select(this.value,'strategy_model_other_cover')">
                  <option value="">Start typing the intervention name</option>
                  <?php    
                    $sql = "select id,strategy_name from strategy_model where strategy_type='6'";
                    $strategy_items= mysql_query($sql);               
					while($items=mysql_fetch_array($strategy_items))
					{ ?>
						<option <?php if($intervention_row['strategy_model']==$items[1]){echo 'selected="selected"';} ?> value="<?php echo $items[1]?>"><?php echo $items[1]?></option>
                  <?php } ?>                  
                  </select>
                  <div class="form-group" style="<?php if($intervention_row['strategy_model']=='Other' && $intervention_row['EBP']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="strategy_model_other_cover" >
                    <label></label>
                    <input type="text" placeholder="Describe" class="form-control" name="strategy_model_other" id="strategy_model_other_text" value="<?php echo $intervention_row['strategy_model_other']; ?>" />
                 </div>
                </div>
                </div>
                <div class="form-group">
                        <div class="row">
                            <span class="col-md-12 col-sm-12 col-xs-12 form-group">
                             <label><span class="text_blue bold">3a. </span>Upload supporting documentation</label>
                             <div id="fine-uploader-manual-trigger"></div>
                                <script type="text/template" id="qq-template-manual-trigger">
                                    <div id="item_uploader_partD" class="qq-uploader-selector qq-uploader" style="<?php echo ($PartD!='' ? 'display:none' : '');  ?>"  qq-drop-area-text="Drop files here">
                                        <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                                            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                                        </div>
                                        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                                            <span class="qq-upload-drop-area-text-selector"></span>
                                        </div>
                                        <div class="buttons">
                                            <div class="qq-upload-button-selector qq-upload-button">
                                                <div><i class="fa fa-x fa-cloud-upload" style="padding-right: 10px;"></i>Upload</div>
                                            </div>
                                        </div>
                                        <span class="qq-drop-processing-selector qq-drop-processing">
                                            <span>Processing dropped files...</span>
                                            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                                        </span>
                                        <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                                            <li>
                                                <div class="qq-progress-bar-container-selector">
                                                    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                                                </div>
                                                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                                                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                                                <span class="qq-upload-file-selector qq-upload-file"></span>
                                                <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                                                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                                                <span class="qq-upload-size-selector qq-upload-size"></span>
                                                <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                                                <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                                                <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                                                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                                            </li>
                                        </ul>
        
                                        <dialog class="qq-alert-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Close</button>
                                            </div>
                                        </dialog>
        
                                        <dialog class="qq-confirm-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">No</button>
                                                <button type="button" class="qq-ok-button-selector">Yes</button>
                                            </div>
                                        </dialog>
        
                                        <dialog class="qq-prompt-dialog-selector">
                                            <div class="qq-dialog-message-selector"></div>
                                            <input type="text">
                                            <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Cancel</button>
                                                <button type="button" class="qq-ok-button-selector">Ok</button>
                                            </div>
                                        </dialog>
                                    </div>
                                </script>
                            </span> 
                      <div class="col-md-12 col-sm-12 col-xs-12 form-group" id="ebp_uploads">      
                      <?php if($intervention_row['uploadfilename']!='') {
                      $arrayfoldername=unserialize($intervention_row['uploadfoldername']);
                      $arrayfilename=unserialize($intervention_row['uploadfilename']);
                      $fileCount = count($arrayfilename);  
                      ?>
                      <label class="mar_t20">Uploaded Items:</label>
                      <ul class="ebp_uploads">
                      <?php for($f=0;$f<$fileCount;$f++){   if(!empty($arrayfilename[$f])&&!empty($arrayfilename[$f])){?>
                      <li id="upload_item<?php echo $f; ?>"><a class="pad_r10" title="<?php echo $arrayfilename[$f]; ?>" target="_blank" href="<?php echo $site_url; ?>/assets/uploader/php-traditional-server/files/<?php echo $arrayfoldername[$f]; ?>/<?php echo $arrayfilename[$f]; ?>">Item<?php echo $f+1; ?></a><i id="remove_uploads<?php echo $f; ?>" class="fa fa-trash" onclick="remove_uploads(<?php echo $f; ?>,<?php echo $intervention_row['id']; ?>)"></i></li> 
                      <?php }}?>
                      </ul>
                      
                      <?php } ?>
                      </div>
                            
                        </div>
                      </div>
					    <div class="form-group">
              <label><span class="text_blue bold">4. </span>What is/are the intended purpose(s) of the communication or information that you will disseminate for this intervention? (Select all that apply)</label>
              <select class="form-control" name="in_intended_purpose" id="in_intended_purpose" onchange="toogle_select(this.value,'in_intended_purpose_other_cover')">
					<option <?php if($intervention_row['in_intended_purpose']=='To raise awareness of one or more specific substance use prevention problems in the community') {echo 'selected="selected"';} ?> value="To raise awareness of one or more specific substance use prevention problems in the community">To raise awareness of one or more specific substance use prevention problems in the community</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To gain support from the community for substance abuse prevention efforts') {echo 'selected="selected"';} ?> value="To gain support from the community for substance abuse prevention efforts">To gain support from the community for substance abuse prevention efforts</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To provide information on community norms related to substance use') {echo 'selected="selected"';} ?> value="To provide information on community norms related to substance use">To provide information on community norms related to substance use</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To provide information on the risks of substance use') {echo 'selected="selected"';} ?> value="To provide information on the risks of substance use">To provide information on the risks of substance use</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To provide substance abuse prevention information (e.g., information on securing prescription drugs in the household, information on resisting alcohol offers)') {echo 'selected="selected"';} ?> value="To provide substance abuse prevention information (e.g., information on securing prescription drugs in the household, information on resisting alcohol offers)">To provide substance abuse prevention information (e.g., information on securing prescription drugs in the household, information on resisting alcohol offers)</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To change individual behaviors with regard to substance use') {echo 'selected="selected"';} ?> value="To change individual behaviors with regard to substance use">To change individual behaviors with regard to substance use</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To provide intervention program information (e.g., contact information, meeting times)') {echo 'selected="selected"';} ?> value="To provide intervention program information (e.g., contact information, meeting times)">To provide intervention program information (e.g., contact information, meeting times)</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To provide surveillance and monitoring information (e.g., information about whom to contact if you suspect underage alcohol sales at a particular establishment)') {echo 'selected="selected"';} ?> value="To provide surveillance and monitoring information (e.g., information about whom to contact if you suspect underage alcohol sales at a particular establishment)">To provide surveillance and monitoring information (e.g., information about whom to contact if you suspect underage alcohol sales at a particular establishment)</option>
						<option <?php if($intervention_row['in_intended_purpose']=='To provide a directory of substance use prevention resources in the community') {echo 'selected="selected"';} ?> value="To provide a directory of substance use prevention resources in the community">To provide a directory of substance use prevention resources in the community</option>
						<option <?php if($intervention_row['in_intended_purpose']=='Other') {echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
					</select>
					
					<div class="form-group" style="<?php if($intervention_row['in_intended_purpose']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_intended_purpose_other_cover" >
                        <label></label>
                        <input type="text" placeholder="Describe" class="form-control" name="in_intended_purpose_other" id="in_intended_purpose_text" value="<?php echo $intervention_row['in_intended_purpose_other']; ?>" />
                    </div>
            </div>
		
				<div class="form-group">  
                  <label class="ele_bl">
                    <label class="ele_bl"><span class="text_blue bold">5. </span> Does this intervention include communication efforts to raise community awareness of underage alcohol use, marijuana use, or prescription drug misuse problems? 
                    <span class="info-badge2 text-lowercase mar_l10" onclick="info_toggle('in_comm_effort_information')" data-toggle ="tooltip" data-placement ="left" title="Click to view Info">i</span></label>
                    <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 in_comm_effort_information" style="display: none;">
    				  <div class="custom-blockquote mar_b20">
    					<p class="mar0">Awareness-raising activities are those in which your primary purpose is to draw attention to a substance abuse problem or to your prevention efforts. These activities are not intended to recruit participants or volunteers, although that may happen.</p>
    				  </div>
    			     </div>
                     </div>
                    </label>  
					  <label class="checkbox_normal mar_r15">
						  <input type="radio" name="in_comm_effort" onchange="hide_show('in_comm_effort','in_comm_effort_cover')" value="Yes" <?php if($intervention_row['in_comm_effort']=='Yes'){echo 'checked="checked"';} ?>>
						  <span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal">
						  <input type="radio" name="in_comm_effort" onchange="hide_show('in_comm_effort','in_comm_effort_cover')" value="No" <?php if($intervention_row['in_comm_effort']=='No'){echo 'checked="checked"';} ?>>
						  <span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				<div style="<?php if($intervention_row['in_comm_effort']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_comm_effort_cover">
				<div class="form-group">
				<label><span class="text_blue bold">6. </span> Indicate the community members and groups (i.e., target audience) to whom you are presenting awareness-raising information. (Select all that apply)</label>
					<select class="form-control resources_select" multiple="" name="in_comm_mem[]" id="in_comm_mem" onchange="toggle_multiselect(this.value,'in_comm_mem','in_comm_mem_other_cover')">
					  <option <?php if(in_array('The general public',$in_comm_mem)) {echo 'selected="selected"';} ?> value="The general public">The general public</option>
					  <option <?php if(in_array('Youth groups or representatives',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Youth groups or representatives">Youth groups or representatives</option>
					  <option <?php if(in_array('Schools or school districts',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Schools or school districts">Schools or school districts</option>
					  <option <?php if(in_array('Youth-serving organizations other than schools (e.g., Big Brothers/Big Sisters, Boy Scouts/Girl Scouts)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Youth-serving organizations other than schools (e.g., Big Brothers/Big Sisters, Boy Scouts/Girl Scouts)">Youth-serving organizations other than schools (e.g., Big Brothers/Big Sisters, Boy Scouts/Girl Scouts)</option>
					  <option <?php if(in_array('Parents, family, or caregiver groups',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Parents, family, or caregiver groups">Parents, family, or caregiver groups</option>
					  <option <?php if(in_array('Advocacy volunteers',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Advocacy volunteers">Advocacy volunteers</option>
					  <option <?php if(in_array('Business community',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Business community">Business community</option>
					  <option <?php if(in_array('Media (e.g., radio and television stations, newspapers and magazines)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Media (e.g., radio and television stations, newspapers and magazines)">Media (e.g., radio and television stations, newspapers and magazines)</option>
					  <option <?php if(in_array('Faith-based organizations (e.g., churches, charitable organizations with religious affiliations such as Catholic Charities)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Faith-based organizations (e.g., churches, charitable organizations with religious affiliations such as Catholic Charities)">Faith-based organizations (e.g., churches, charitable organizations with religious affiliations such as Catholic Charities)</option>
					  <option <?php if(in_array('Civic or volunteer organizations (e.g., Kiwanis, Fraternal Order of Police, Women’s League, local sports or neighborhood associations)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Civic or volunteer organizations (e.g., Kiwanis, Fraternal Order of Police, Women’s League, local sports or neighborhood associations)">Civic or volunteer organizations (e.g., Kiwanis, Fraternal Order of Police, Women’s League, local sports or neighborhood associations)</option>
					  <option <?php if(in_array('LGBTQ-supportive organization',$in_comm_mem)) {echo 'selected="selected"';} ?> value="LGBTQ-supportive organization">LGBTQ-supportive organization</option>
					  <option <?php if(in_array('Military or veteran organization',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Military or veteran organization">Military or veteran organization</option>
					  <option <?php if(in_array('Law enforcement agencies (e.g., local, tribal, State, and Federal law enforcement agencies, including the police, the Federal Bureau  of Investigation [FBI], the Drug Enforcement Administration [DEA], and the Bureau of Alcohol, Tobacco, Firearms and Explosives [ATF])',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Law enforcement agencies (e.g., local, tribal, State, and Federal law enforcement agencies, including the police, the Federal Bureau  of Investigation [FBI], the Drug Enforcement Administration [DEA], and the Bureau of Alcohol, Tobacco, Firearms and Explosives [ATF])">Law enforcement agencies (e.g., local, tribal, State, and Federal law enforcement agencies, including the police, the Federal Bureau  of Investigation [FBI], the Drug Enforcement Administration [DEA], and the Bureau of Alcohol, Tobacco, Firearms and Explosives [ATF])</option>
					  <option <?php if(in_array('Local or State, tribal, or jurisdiction courts',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Local or State, tribal, or jurisdiction courts">Local or State, tribal, or jurisdiction courts</option>
					  <option <?php if(in_array('State departments of justice (e.g., judicial department, department of juvenile justice, department of criminal justice, attorney general’s office)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="State departments of justice (e.g., judicial department, department of juvenile justice, department of criminal justice, attorney general’s office)">State departments of justice (e.g., judicial department, department of juvenile justice, department of criminal justice, attorney general’s office)</option>
					  <option <?php if(in_array('U.S. Department of Justice (excluding their law enforcement arms mentioned above [i.e., FBI, DEA, ATF])',$in_comm_mem)) {echo 'selected="selected"';} ?> value="U.S. Department of Justice (excluding their law enforcement arms mentioned above [i.e., FBI, DEA, ATF])">U.S. Department of Justice (excluding their law enforcement arms mentioned above [i.e., FBI, DEA, ATF])</option>
					  <option <?php if(in_array('State, tribal, or local jails and prisons',$in_comm_mem)) {echo 'selected="selected"';} ?> value="State, tribal, or local jails and prisons">State, tribal, or local jails and prisons</option>
					  <option <?php if(in_array('Health care professionals',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Health care professionals">Health care professionals</option>
					  <option <?php if(in_array('State, tribal, jurisdiction, or local public health departments',$in_comm_mem)) {echo 'selected="selected"';} ?> value="State, tribal, jurisdiction, or local public health departments">State, tribal, jurisdiction, or local public health departments</option>
					  <option <?php if(in_array('Mental health professionals or agencies',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Mental health professionals or agencies">Mental health professionals or agencies</option>
					  <option <?php if(in_array('Other State, tribal, or jurisdiction government agencies (e.g., public health, public safety, social services, American Indian tribal government)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Other State, tribal, or jurisdiction government agencies (e.g., public health, public safety, social services, American Indian tribal government)">Other State, tribal, or jurisdiction government agencies (e.g., public health, public safety, social services, American Indian tribal government)</option>
					  <option <?php if(in_array('Local, village, or tribal agencies (mayor’s office, city council, tribal council, Alaska Native Corporation agencies)',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Local, village, or tribal agencies (mayor’s office, city council, tribal council, Alaska Native Corporation agencies)">Local, village, or tribal agencies (mayor’s office, city council, tribal council, Alaska Native Corporation agencies)</option>
					  <option <?php if(in_array('Other',$in_comm_mem)) {echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
					</select>
					
					<div class="form-group" style="<?php if(in_array('Other',$in_comm_mem)){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_comm_mem_other_cover" >
                        <label></label>                        
                        <input type="text" placeholder="Describe" name="in_comm_mem_other" class="form-control" id="in_comm_mem_other_text" value="<?php echo $intervention_row['in_comm_mem_other']; ?>" />
                    </div>
				</div>
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">7. </span> Indicate the total number of different community groups or organizations to whom you  will be presenting awareness-raising information this year.</label>
					 <input type="number" class="form-control number_only" min="0" name="in_com_group" id="in_com_group" value="<?php echo $intervention_row['in_com_group']; ?>">
				</div>
				</div>
				
				<div class="form-group">
				 <label class="ele_bl">
                    <label class="ele_bl"><span class="text_blue bold">8. </span> For this intervention, will you implement a social marketing campaign this year?
					<span class="info-badge2 text-lowercase mar_l10" onclick="info_toggle('in_social_market_information')" data-toggle ="tooltip" data-placement ="left" title="Click to view Info">i</span></label>
					<div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 in_social_market_information" style="display: none;">
    				  <div class="custom-blockquote mar_b20">
    					<p class="mar0">Social marketing is using the principles of commercial marketing to develop, implement, and evaluate programs designed to influence the behavior of a target audience. Rather than dictating the way that information is to be conveyed, social marketing involves listening to the needs and desires of the target audience and building the program from there.</p>
    				  </div>
    			     </div>
                     </div>
				 </label>
					   <label class="checkbox_normal mar_r15">
						<input type="radio" name="in_social_market" value="Yes" <?php if($intervention_row['in_social_market']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					  </label>
					  <label class="checkbox_normal">
						<input type="radio" name="in_social_market" value="No" <?php if($intervention_row['in_social_market']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					  </label> 
				</div>
				 <div class="form-group">
              <label><span class="text_blue bold">9. </span> Age groups targeted for information dissemination (and other communication activities)(Select all that apply)</label>
              <select class="form-control" name="types_of_participants" id="types_of_participants" onchange="toogle_select(this.value,'types_of_participants_other_cover')">
              <option  value="">Select</option>
             <option <?php if($intervention_row['types_of_participants']=='Children age 0-11'){echo 'selected="selected"';} ?> value="Children age 0-11">Children age 0-11</option>
						  <option <?php if($intervention_row['types_of_participants']=='Youth age 12-17'){echo 'selected="selected"';} ?> value="Youth age 12-17">Youth age 12-17</option>
						  <option <?php if($intervention_row['types_of_participants']=='Young adults age 18-20'){echo 'selected="selected"';} ?> value="Young adults age 18-20">Young adults age 18-20</option>
						  <option <?php if($intervention_row['types_of_participants']=='Young adults age 21-25'){echo 'selected="selected"';} ?> value="Young adults age 21-25">Young adults age 21-25</option>
						  <option <?php if($intervention_row['types_of_participants']=='Adults age 26 or older'){echo 'selected="selected"';} ?> value="Adults age 26 or older">Adults age 26 or older</option>
						  <option <?php if($intervention_row['types_of_participants']=='Other'){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
              
              </select>
               <div class="form-group" style="<?php if($intervention_row['types_of_participants']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="types_of_participants_other_cover" >
								<label></label>
								<input type="text" placeholder="Describe" class="form-control" name="types_of_participants_other" id="types_of_participants_other_text" value="<?php echo $intervention_row['types_of_participants_other']; ?>" />
						  </div>
            </div>
           
				  <div class="form-group">
              <label><span class="text_blue bold">10. </span>Population type(s) targeted by the information dissemination (and other communication activities) (Select all that apply)</label>
              <select class="form-control" name="target_population" id="target_population" onchange="toogle_select(this.value,'target_population_other_cover')">
						<option <?php if($intervention_row['target_population']=='Middle school students') {echo 'selected="selected"';} ?> value="Middle school students">Middle school students</option>
						<option <?php if($intervention_row['target_population']=='High school students') {echo 'selected="selected"';} ?> value="High school students">High school students</option>
						<option <?php if($intervention_row['target_population']=='College students') {echo 'selected="selected"';} ?> value="College students">College students</option>
						<option <?php if($intervention_row['target_population']=='Parents') {echo 'selected="selected"';} ?> value="Parents">Parents</option>
						<option <?php if($intervention_row['target_population']=='Health care providers') {echo 'selected="selected"';} ?> value="Health care providers">Health care providers</option>
						<option <?php if($intervention_row['target_population']=='Employees') {echo 'selected="selected"';} ?> value="Employees">Employees</option>
						<option <?php if($intervention_row['target_population']=='Current or former military members') {echo 'selected="selected"';} ?> value="Current or former military members">Current or former military members</option>
						<option <?php if($intervention_row['target_population']=='Military family members') {echo 'selected="selected"';} ?> value="Military family members">Military family members</option>
						<option <?php if($intervention_row['target_population']=='Lesbian/gay/bisexual/transgender/questioning individuals (LGBTQ)') {echo 'selected="selected"';} ?> value="Lesbian/gay/bisexual/transgender/questioning individuals (LGBTQ)">Lesbian/gay/bisexual/transgender/questioning individuals (LGBTQ)</option>
						<option <?php if($intervention_row['target_population']=='Individuals living in poverty') {echo 'selected="selected"';} ?> value="Individuals living in poverty">Individuals living in poverty</option>
						<option <?php if($intervention_row['target_population']=='Individuals whose native language is other than English') {echo 'selected="selected"';} ?> value="Individuals whose native language is other than English">Individuals whose native language is other than English</option>
						<option <?php if($intervention_row['target_population']=='Individuals with low literacy') {echo 'selected="selected"';} ?> value="Individuals with low literacy">Individuals with low literacy</option>
						<option <?php if($intervention_row['target_population']=='Individuals with mental illness') {echo 'selected="selected"';} ?> value="Individuals with mental illness">Individuals with mental illness</option>
						<option <?php if($intervention_row['target_population']=='Individuals with disabilities (e.g., hearing, visually, or physically impaired)') {echo 'selected="selected"';} ?> value="Individuals with disabilities (e.g., hearing, visually, or physically impaired)">Individuals with disabilities (e.g., hearing, visually, or physically impaired)</option>
						<option <?php if($intervention_row['target_population']=='Other') {echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
					</select>
              <div class="form-group" style="<?php if($intervention_row['target_population']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="target_population_other_cover" >
							<label></label>
							<input type="text" placeholder="Describe" class="form-control" name="target_population_other" value="<?php echo $intervention_row['target_population_other'];  ?>" id="target_population_other_text" />
					   </div>
            </div>
			
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">11. </span>For this particular information dissemination (and other communication activities) will you create or air television ads?    </label>
						<label class="checkbox_normal mar_r15">
						  <input type="radio" name="in_tele_ad" onchange="hide_show('in_tele_ad','in_tele_ad_cover')" value="Yes" <?php if($intervention_row['in_tele_ad']=='Yes'){echo 'checked="checked"';} ?>>
						  <span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal">
						  <input type="radio" name="in_tele_ad" onchange="hide_show('in_tele_ad','in_tele_ad_cover')" value="No" <?php if($intervention_row['in_tele_ad']=='No'){echo 'checked="checked"';} ?>>
						  <span class="custom-icon radio-icon"></span>No
						</label>		
				</div>
				<div style="<?php if($intervention_row['in_tele_ad']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_tele_ad_cover">
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">11a. </span>How many individual times will the television ads air this year? </label>
						<input type="number" class="form-control in_time_tele_ad" min="0" id="in_time_tele_ad" name="in_time_tele_ad" value="<?php echo $intervention_row['in_time_tele_ad']; ?>">
				</div>
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">11b. </span>How many weeks will the television  ads air this year?  </label>
						<input type="number" class="form-control number_only" min="0" id="in_week_tele_ad" name="in_week_tele_ad" value="<?php echo $intervention_row['in_week_tele_ad']; ?>">
				</div>
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">11c. </span>On how many different television stations will the ad(s) be aired this year?  </label>
					<select class="form-control" name="in_diff_tele_station">
						<?php for($j=1;$j<=30;$j++){?>
                                <option <?php if($intervention_row['in_diff_tele_station']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					</select>
				</div>
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">11d. </span>Provide any information you have on the reach of the television ads aired this year. This could include information provided by the television stations or advertising agency on ratings points; the average number of listeners at the time the ads aired; the geographic area where the ads aired; and the target audience of the related television programs  </label>
						<textarea class="form-control" placeholder="Describe - 3,000 character limit" name="in_detail_tele_information" id="in_detail_tele_information"><?php echo $intervention_row['in_detail_tele_information']?></textarea>
				</div>
				</div>
				
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">12. </span>For this particular information dissemination (and other create or air radio ads this year? </label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_radio_ad" onchange="hide_show('in_radio_ad','in_radio_ad_cover')" value="Yes" <?php if($intervention_row['in_radio_ad']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_radio_ad" onchange="hide_show('in_radio_ad','in_radio_ad_cover')" value="No" <?php if($intervention_row['in_radio_ad']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				
				<div style="<?php if($intervention_row['in_radio_ad']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_radio_ad_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">12a. </span>How many individual times will the radio ads air this year? </label>
						<input type="number" class="form-control number_only" min="0" name="in_time_radio_ad" id="in_time_radio_ad" value="<?php echo $intervention_row['in_time_radio_ad']; ?>">
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">12b. </span>How many weeks will the radio ads air this year?  </label>
						<select class="form-control" name="in_week_radio_ad">
							<?php for($j=1;$j<=52;$j++){  if($j>1) $txt = "Weeks"; else $txt = "Week";?>
                                <option <?php if($intervention_row['in_week_radio_ad']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j. ' '.$txt; ?></option>
							<?php }?>
						</select>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">12c. </span>On how many different radio stations will the ad(s) be aired this year?  </label>
						<select class="form-control" name="in_diff_radio_station">
							<?php for($j=1;$j<=30;$j++){?>
                                <option <?php if($intervention_row['in_diff_radio_station']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
							<?php }?>
						</select>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">12d. </span>Provide any information you have on the reach of the radio ads aired this year. This could include information provided by the radio stations or advertising agency on ratings points; the average number of listeners at the time the ads aired; the geographic area where the ads aired; and the target audience of the related radio broadcast. </label>
						<textarea class="form-control" placeholder="Describe - 3,000 character limit" name="in_detail_radio_information" id="in_detail_radio_information"><?php echo $intervention_row['in_detail_radio_information']; ?></textarea>
				</div>
				</div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">13. </span>For this particular communication strategy, will you create or publish print ads this year?  </label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_print_ad" onchange="hide_show('in_print_ad','in_print_ad_cover')" value="Yes" <?php if($intervention_row['in_print_ad']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_print_ad" onchange="hide_show('in_print_ad','in_print_ad_cover')" value="No" <?php if($intervention_row['in_print_ad']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>				
				<div style="<?php if($intervention_row['in_print_ad']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_print_ad_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">13a. </span>For this particular communication strategy, will you create or publish print ads this year?  </label>
						<input type="number" class="form-control number_only" id="in_create_print_ad" name="in_create_print_ad" value="<?php echo $intervention_row['in_create_print_ad']; ?>">
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">13a. </span>How many different newspapers or magazines will display the ads this year?  </label>
						<select class="form-control" name="in_diff_print_ad">
							<?php for($j=1;$j<=30;$j++){?>
                                <option <?php if($intervention_row['in_diff_print_ad']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
							<?php }?>
						</select>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">13b. </span> Provide any information you have on the reach of the print ads  to be published this year. This could include information provided by the newspaper or magazine on its average readership; 10the geographic area in which the publication was distributed; and the target audience of the publication.  </label>
						<textarea class="form-control" placeholder="Describe - 3,000 character limit" name="in_detail_print_ad" id="in_detail_print_ad"><?php echo $intervention_row['in_detail_print_ad']; ?></textarea>
				</div>
				</div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">14. </span>For this particular information dissemination (and other communication activities) will you host special events (e.g. alcohol-free concert, invited speakers) this year? </label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_spl_event" onchange="hide_show('in_spl_event','in_spl_event_cover')" value="Yes" <?php if($intervention_row['in_spl_event']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_spl_event" onchange="hide_show('in_spl_event','in_spl_event_cover')" value="No" <?php if($intervention_row['in_spl_event']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>				
				<div class="form-group" style="<?php if($intervention_row['in_spl_event']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_spl_event_cover">
				<div class="form-group">
					<label class="ele_bl"><span class="text_blue bold">14a. </span>What types of special events will you host this year? </label>
						<input type="text" class="form-control" name="in_spl_event_type" id="in_spl_event_type" value="<?php echo $intervention_row['in_spl_event_type']; ?>">
				</div>
				<div class="form-group">
					<label class="ele_bl"><span class="text_blue bold">14b. </span>How many total special events will you host this year?  </label>
						<input type="number" class="form-control" min="0" name="in_spl_event_tot" id="in_spl_event_tot" value="<?php echo $intervention_row['in_spl_event_tot']; ?>">
				</div>
				</div>
				<div class="form-group">
				<label class="ele_bl"><span class="text_blue bold">15. </span>For this particular information dissemination (and other communication activities) will you take part in other promotional activities (e.g. providing alcohol-free pamphlets at health fair, distributing prescription drug take-back information at school) this year? </label>
				  <label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_promotional_activities" onchange="hide_show('in_promotional_activities','in_promotional_activities_cover')" value="Yes" <?php if($intervention_row['in_promotional_activities']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_promotional_activities" onchange="hide_show('in_promotional_activities','in_promotional_activities_cover')" value="No" <?php if($intervention_row['in_promotional_activities']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				
				<div class="form-group" style="<?php if($intervention_row['in_promotional_activities']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_promotional_activities_cover">
					<div class="form-group">
					<label class="ele_bl"><span class="text_blue bold">15a. </span>What types of promotional activities will you participate in this year?  </label>
						<input type="text" class="form-control" name="in_promotional_activities_type" id="in_promotional_activities_type" value="<?php echo $intervention_row['in_promotional_activities_type']; ?>">
					</div>
					<div class="form-group">
					<label class="ele_bl"><span class="text_blue bold">15b. </span>How many promotional activities will you take participate in this year?  </label>
						<input type="number" class="form-control number_only" min="0" name="in_promotional_activities_tot" id="in_promotional_activities_tot" value="<?php echo $intervention_row['in_promotional_activities_tot']; ?>">
					</div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">16. </span>For this particular information dissemination (and other communication activities), will you present at community meetings (e.g., parent-teacher association [PTA] meetings, town hall meetings, school assemblies, NPU meetings, etc.) this year? Community meetings do not include regularly scheduled coalition meetings or coalition meetings held for planning purposes.</label>
. 						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_com_met" onchange="hide_show('in_com_met','in_com_met_cover')" value="Yes" <?php if($intervention_row['in_com_met']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_com_met" onchange="hide_show('in_com_met','in_com_met_cover')" value="No" <?php if($intervention_row['in_com_met']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				
				<div class="form-group" style="<?php if($intervention_row['in_com_met']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_com_met_cover">
					<div class="form-group">
					<label class="ele_bl"><span class="text_blue bold">16a. </span>At how many community meetings will you present this coming year? </label>
						<input type="number" class="form-control number_only" min="0" name="in_num_com_met" id="in_num_com_met" value="<?php echo $intervention_row['in_num_com_met']; ?>"/>
					</div>
					<?php 
					$audience_members=($intervention_row['in_group_com_met']!=''?unserialize($intervention_row['in_group_com_met']):$intervention_row['in_group_com_met']);
				
                    //$audience_members_count=(is_array($audience_members)?count($audience_members):0);
					?>
					<div class="form-group" id="audience_members">
					<label class="ele_bl"><span class="text_blue bold">16b. </span>To which group(s)/audience members will you be presenting this year?  </label>
                    
						<?php $o=0; if($audience_members!='') { foreach($audience_members as $act) { $o++;  if($o%2==0) { ?>
                       <div class="row" > <?php } ?>
                          <div class="col-md-6 "><input class="form-control number_only" name="in_group_com_met[]" value="<?php echo $act;?>" type="text"></div>
                           
                        <?php if($o%2==0) { ?> </div><br> <?php } } } else { $o=1; ?>
                        <div class="row" >
                        <div class="col-md-6 "><input class="form-control number_only" name="in_group_com_met[]" type="text"></div>
                          <div class="col-md-6 "><input class="form-control number_only" name="in_group_com_met[]" type="text"></div>
                          </div><br>
                          <?php } ?>
					</div>
					<div class="col-md-12 form">
                   <input type="hidden" id="audience_members_count" value="<?php echo $o;?>" /> 
                   <button type="button" class="add_button mar_b15" onclick="add_audience(document.getElementById('audience_members_count').value)"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add</button>
                </div>
				</div>
				</div>
				</div>
				
				<div class="col-md-6">
				<div class="form">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">17. </span>For this particular information dissemination (and other communication activities) will you send letters to the editor of the local newspaper or community newsletters this year? </label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_letter" onchange="hide_show('in_letter','in_letter_cover')" value="Yes" <?php if($intervention_row['in_letter']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_letter" onchange="hide_show('in_letter','in_letter_cover')" value="No" <?php if($intervention_row['in_letter']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				
				<div class="form-group" style="<?php if($intervention_row['in_letter']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_letter_cover">
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">17a. </span>To how many newspapers or newsletters will you send letters to the editor this year? </label>
							<input type="number" class="form-control" min="0" name="in_num_letter" id="in_num_letter" value="<?php echo $intervention_row['in_num_letter']; ?>">
					</div>
				</div>
                <div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">18. </span>For this particular information dissemination (and other communication activities) will you develop or broadcast substance abuse prevention PSAs this year? </label>
					<label class="checkbox_normal mar_r15 mar_t5">
						<input type="radio" name="in_PSA" onchange="hide_show('in_PSA','in_PSA_cover')" value="Yes" <?php if($intervention_row['in_PSA']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					</label>
					<label class="checkbox_normal mar_t5"> 
						<input type="radio" name="in_PSA" onchange="hide_show('in_PSA','in_PSA_cover')" value="No" <?php if($intervention_row['in_PSA']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					</label>
				</div>
				<div class="form-group" style="<?php if($intervention_row['in_PSA']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_PSA_cover">
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">18a. </span>How many individual times will the PSAs air this year?  </label>
							<input type="number" class="form-control number_only" min="0" name="in_time_PSA" id="in_time_PSA" value="<?php echo $intervention_row['in_time_PSA']; ?>">
					</div>
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">18b. </span>For how many weeks will the PSAs air this year?  </label>
							<select class="form-control" name="in_week_PSA">
								<?php for($j=1;$j<=52;$j++){ if($j>1) $jtxt = "Weeks"; else $jtxt = "Week";?>
									<option <?php if($intervention_row['in_week_PSA']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j.' '.$jtxt; ?></option>
								<?php }?>
							</select>
					</div>
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">18c. </span>On how many different radio or TV stations will the PSAs be aired this year? </label>
							<select class="form-control" name="in_diff_PSA">
								<?php for($j=1;$j<=40;$j++){?>
									<option <?php if($intervention_row['in_diff_PSA']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
								<?php }?>
							</select>
					</div>
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">18d. </span>Provide any information you have on the reach of the PSAs to be aired this year. This could include information provided by the radio station,  television station, or advertising agency on ratings points; the average number of listeners or viewers at the time the ads aired; the geographic area where the ads aired; and the target audience of the related  broadcast.  </label>
							<textarea class="form-control" placeholder="Describe - 3,000 character limit" name="in_detail_PSA" id="in_detail_PSA"><?php echo $intervention_row['in_detail_PSA']; ?></textarea>
					</div>
				</div>			               		
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19. </span>For this particular information dissemination (and other communication activities) will you produce and/or distribute prevention posters this year?</label>
					   <label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_prevention_poster" onchange="hide_show('in_prevention_poster','in_prevention_poster_cover')" value="Yes" <?php if($intervention_row['in_prevention_poster']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_prevention_poster" onchange="hide_show('in_prevention_poster','in_prevention_poster_cover')" value="No" <?php if($intervention_row['in_prevention_poster']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				<div style="<?php if($intervention_row['in_prevention_poster']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_prevention_poster_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19a. </span>At how many locations or to how many organizations will you provide prevention posters this year?  </label>
					   <input type="number" class="form-control number_only" min="0" name="in_num_prevention_poster" id="in_num_prevention_poster" value="<?php echo $intervention_row['in_num_prevention_poster']; ?>">
				</div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19b. </span>What is the total number of posters you will distribute this year? </label>
					   <input type="number" class="form-control number_only" min="0" name="in_prevention_poster_tot" id="in_prevention_poster_tot" value="<?php echo $intervention_row['in_prevention_poster_tot']; ?>">
				</div>                
                </div>	
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20. </span> For this particular information dissemination (and other communication activities) will you produce and/or distribute brochures this year? </label>
					<label class="checkbox_normal mar_r15 mar_t5">
						<input type="radio" name="in_distribute" onchange="hide_show('in_distribute','in_distribute_cover')" value="Yes" <?php if($intervention_row['in_distribute']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					</label>
					<label class="checkbox_normal mar_t5"> 
						<input type="radio" name="in_distribute" onchange="hide_show('in_distribute','in_distribute_cover')" value="No" <?php if($intervention_row['in_distribute']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					</label>
			    </div>
				<div style="<?php if($intervention_row['in_distribute']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_distribute_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20a. </span>At how many locations or to how many organizations will you provide brochures this year?  </label>
						<input type="number" class="form-control number_only" min="0" name="in_distribute_location" id="in_distribute_location" value="<?php echo $intervention_row['in_distribute_location']; ?>">
                </div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20b. </span>What is the total number of brochures you will distribute this year? </label>
					  <input type="number" class="form-control number_only" min="0" name="in_distribute_tot" id="in_distribute_tot" value="<?php echo $intervention_row['in_distribute_tot']; ?>">
			    </div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">21. </span>For this particular information dissemination (and other communication activities) will you display billboards this year? </label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_display_bill" onchange="hide_show('in_display_bill','in_display_bill_cover')" value="Yes" <?php if($intervention_row['in_display_bill']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_display_bill" onchange="hide_show('in_display_bill','in_display_bill_cover')" value="No" <?php if($intervention_row['in_display_bill']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
                <div style="<?php if($intervention_row['in_display_bill']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_display_bill_cover" >
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">21a. </span>At how many locations will billboards be displayed this year? </label>
						<input type="number" class="form-control number_only" min="0" name="in_display_bill_loc" id="in_display_bill_loc" value="<?php echo $intervention_row['in_display_bill_loc']; ?>">
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">21b. </span>For how many weeks will billboards be displayed this year? </label>
						<select class="form-control" name="in_week_display_bill">
							<?php for($j=1;$j<=52;$j++){  if($j>1) $ftxt = "Weeks"; else $ftxt = "Week";?>
								<option <?php if($intervention_row['in_week_display_bill']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j.' '.$ftxt; ?></option>
							<?php }?>
						</select>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">21c. </span>Provide any information you have on the reach of the billboards to be displayed this year. This could include information provided by advertising agency, the geographic area where the billboards are displayed, and the target audience of the billboard message. </label>
						<textarea class="form-control" placeholder="Describe - 3,000 character limit" name="in_detail_display_bill" id="in_detail_display_bill"><?php echo $intervention_row['in_detail_display_bill']; ?></textarea>
				</div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">22. </span>For this particular information dissemination (and other communication activities) will you provide a related information line or hotline this year?  </label>
					  <label class="checkbox_normal mar_r15">
						<input type="radio" name="in_hotline" value="Yes" <?php if($intervention_row['in_hotline']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					  </label>
					  <label class="checkbox_normal">
						<input type="radio" name="in_hotline" value="No" <?php if($intervention_row['in_hotline']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					  </label>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">23. </span>For this particular information dissemination (and other communication activities),  will you develop or run a prevention-focused clearinghouse or information resource center this year? </label>
					  <label class="checkbox_normal mar_r15">
						<input type="radio" name="in_resource_center" value="Yes" <?php if($intervention_row['in_resource_center']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					  </label>
					  <label class="checkbox_normal">
						<input type="radio" name="in_resource_center" value="No" <?php if($intervention_row['in_resource_center']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					  </label>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">24. </span>For this particular information dissemination (and other communication activities), will you launch or continue prevention-focused Web sites or Facebook pages during the past 6 months?  </label>
					  <label class="checkbox_normal mar_r15">
						<input type="radio" name="in_web_sites" value="Yes" <?php if($intervention_row['in_web_sites']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					  </label>
					  <label class="checkbox_normal">
						<input type="radio" name="in_web_sites" value="No" <?php if($intervention_row['in_web_sites']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					  </label>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">25. </span>For this particular information dissemination (and other communication activities), will you conduct a sticker shock campaign to raise awareness about underage drinking?    </label>
					  <label class="checkbox_normal mar_r15">
						<input type="radio" name="in_sticker_shock" onchange="hide_show('in_sticker_shock','in_sticker_shock_cover')" value="Yes" <?php if($intervention_row['in_sticker_shock']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					  </label>
					  <label class="checkbox_normal">
						<input type="radio" name="in_sticker_shock" onchange="hide_show('in_sticker_shock','in_sticker_shock_cover')" value="No" <?php if($intervention_row['in_sticker_shock']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					  </label>
				</div>
				
				<div style="<?php if($intervention_row['in_sticker_shock']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_sticker_shock_cover" >
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">25a. </span>How many alcohol retail outlets are located in your target community(ies)? </label>
						<input type="text" class="form-control number_only" name="in_sticker_shock_target" id="in_sticker_shock_target" value="<?php echo $intervention_row['in_sticker_shock_target']; ?>">
				</div>
				
				<div class="form-group sitting_location_cover">
                    <label class="ele_bl"><span class="text_blue bold">25b. </span>At how many alcohol retail outlets will you implement the sticker shock campaign?  </label>
						<input type="text" class="form-control number_only" name="in_sticker_shock_outlet" id="in_sticker_shock_outlet" value="<?php echo $intervention_row['in_sticker_shock_outlet']; ?>">
                </div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">26. </span>For this particular information dissemination (and other c ommunication activities), will you distribute window clings to raise awareness about underage drinking?   </label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="in_underage_drinking" onchange="hide_show('in_underage_drinking','in_underage_drinking_cover')" value="Yes" <?php if($intervention_row['in_underage_drinking']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="in_underage_drinking" onchange="hide_show('in_underage_drinking','in_underage_drinking_cover')" value="No" <?php if($intervention_row['in_underage_drinking']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
                <div class="form-group" style="<?php if($intervention_row['in_underage_drinking']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_underage_drinking_cover" >
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">26a. </span>How many alcohol retail outlets are located in your target community(ies)? </label>
						<input type="text" class="form-control number_only" name="in_underage_drinking_com" id="in_underage_drinking_com" value="<?php echo $intervention_row['in_underage_drinking_com']; ?>">
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">26b. </span>At how many alcohol retail outlets will you display window clings? </label>
						<input type="text" class="form-control number_only" name="in_underage_drinking_win" id="in_underage_drinking_win" value="<?php echo $intervention_row['in_underage_drinking_win']; ?>">
				</div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">27. </span>For this particular information dissemination (and other communication activities), will you conduct a pharmacy bag campaign to raise awareness about prescription drug misuse/abuse ?  </label>
					<label class="checkbox_normal mar_r15 mar_t5">
						<input type="radio" name="in_drug_abuse" onchange="hide_show('in_drug_abuse','in_drug_abuse_cover')" value="Yes" <?php if($intervention_row['in_drug_abuse']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					</label>
					<label class="checkbox_normal mar_t5"> 
						<input type="radio" name="in_drug_abuse" onchange="hide_show('in_drug_abuse','in_drug_abuse_cover')" value="No" <?php if($intervention_row['in_drug_abuse']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					</label>
				</div>
				<div class="form-group" style="<?php if($intervention_row['in_drug_abuse']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_drug_abuse_cover" >
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">27a. </span>How many pharmacies are located in your target community(ies)?  </label>
							<input type="text" class="form-control number_only" name="in_drug_abuse_com" id="in_drug_abuse_com" value="<?php echo $intervention_row['in_drug_abuse_com']; ?>">
					</div>
					<div class="form-group">
						<label class="ele_bl"><span class="text_blue bold">27b. </span>At how many pharmacies will you implement the sticker shock campaign?   </label>
							<input type="text" class="form-control number_only" name="in_drug_abuse_sticker" id="in_underage_drinking_sticker" value="<?php echo $intervention_row['in_drug_abuse_sticker']; ?>">
					</div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">28. </span>For this particular information dissemination (and other communicat ion activities) service type, will you conduct other communication activities in the next year? </label>
					<label class="checkbox_normal mar_r15 mar_t5">
						<input type="radio" name="in_com_activity" onchange="hide_show('in_com_activity','in_com_activity_cover')" value="Yes" <?php if($intervention_row['in_com_activity']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					</label>
					<label class="checkbox_normal mar_t5"> 
						<input type="radio" name="in_com_activity" onchange="hide_show('in_com_activity','in_com_activity_cover')" value="No" <?php if($intervention_row['in_com_activity']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					</label>
				</div>
				<div class="form-group" style="<?php if($intervention_row['in_com_activity']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="in_com_activity_cover" >
					<textarea class="form-control" placeholder="Describe - 3,000 character limit" name="in_com_activity_desc" id="in_com_activity_desc"><?php echo $intervention_row['in_com_activity_desc']; ?></textarea>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">29. </span>Estimate the total number of individuals to be reached or affected by your information dissemination (and other communication activities) this year. If you are unsure of the exact number of individuals affected, respond with your best estimate.  </label>
						<input type="text" class="form-control" name="in_num_of_individual" id="in_num_of_individual" value="<?php echo $intervention_row['in_num_of_individual']; ?>">
				</div>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-12 text-center form mar_tb30">
        <input type="hidden" name="partd_strategy_type" value="<?php echo $strategy_type ?>" />
            <?php if($_POST['intervention_id']>0){                
                $save_btn=($intervention_row['part_d_save']==0 ? 'savePartD()':'not_save()');                 
            ?>
              <input type="hidden" name="partD_edit" id="partD_edit" value="<?php echo $_POST['intervention_id'] ?>" />   
            <?php }else{
              $save_btn='savePartD()'; 
            ?>
              <input type="hidden" name="partD_save" id="partD_edit" value="1" />               
            <?php }?>
              <a class="mar_r15 button save_btn" onclick="<?php echo $save_btn; ?>" style="padding: 10px 20px;cursor: pointer;">Save</a>
              <a class="mar_l15 button cancel_btn" onclick="editPart(4)" style="padding: 10px 20px;cursor: pointer;">Edit</a>
              
            <span class="text_blue_lighten pad_lr20 ft_18">(Help)</span>
         </div>
      </form>      
      </div>
    </div>
  </div>
  <!-------Part D End---------->
  <!-------Part E Start---------->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" id="parte" <?php if($strategy_type!='' && $strategy_type==$intervention_row['strategy_type']) { echo 'href="#collapse5"'; } else { echo " onclick='alert_error_msg()'"; }?>><!-- href="#collapse5" -->
        <i class="fa fa-angle-down" aria-hidden="true"></i>Part E</a><span>- Plan Intervention work</span>
      </h4>
    </div>
    <div id="collapse5" class="panel-collapse collapse <?php echo $PartE; ?>">
      <div class="panel-body">
      <form id="partEForm">
        <div class="col-md-12" id="bundle_cover">
        <?php 
        $work_bundle             =($intervention_row['work_bundle']!=''              ? unserialize($intervention_row['work_bundle'])              : $intervention_row['work_bundle']);
        $description             =($intervention_row['description']!=''              ? unserialize($intervention_row['description'])              : $intervention_row['description']);
        $start_date              =($intervention_row['start_date']!=''               ? unserialize($intervention_row['start_date'])               : $intervention_row['start_date']);
        $end_date                =($intervention_row['end_date']!=''                 ? unserialize($intervention_row['end_date'])                 : $intervention_row['end_date']);
        $like_training           =($intervention_row['like_training']!=''            ? unserialize(str_replace('-','',$intervention_row['like_training']))            : $intervention_row['like_training']);
        $about_training          =($intervention_row['about_training']!=''           ? unserialize($intervention_row['about_training'])           : $intervention_row['about_training']);
        $responsibilities_parties=($intervention_row['responsibilities_parties']!='' ? unserialize($intervention_row['responsibilities_parties']) : $intervention_row['responsibilities_parties']);
        $action_steps            =($intervention_row['action_steps']!=''             ? unserialize($intervention_row['action_steps'])             : $intervention_row['action_steps']);
        $activities              =($intervention_row['activities']!=''               ? unserialize(str_replace('-','',$intervention_row['activities']))               : $intervention_row['activities']);
        $target_audience         =($intervention_row['target_audience']!=''          ? unserialize($intervention_row['target_audience'])          : $intervention_row['target_audience']);
        $other_target_audience   =($intervention_row['other_target_audience']!=''    ? unserialize($intervention_row['other_target_audience'])    : $intervention_row['other_target_audience']);
        $end_status              =($intervention_row['end_status']!=''    ? unserialize($intervention_row['end_status'])    : $intervention_row['end_status']);
        $ongoing_explain         =($intervention_row['ongoing_explain']!=''    ? unserialize($intervention_row['ongoing_explain'])    : $intervention_row['ongoing_explain']);
        
        $work_bundle_count=1;
        $w=0;
        if($work_bundle!=''){
        $work_bundle_count=count($work_bundle);
        for($w=0;$w<count($work_bundle);$w++){
        ?>
          <div class="row portfolio_workbundle pad_t20">
                      <div class="form-group col-md-12">
                        <label class="add_button ele_bl text_white pad10 mar0 "><span class="bold">1. </span><?php echo $intervention_community; ?></label>
                      </div>
               <div class="col-md-6 col-xs-12">
                    <div class="form">
                      
                      <div class="form-group">
                        <label><span class="text_blue bold">2. </span>Work Bundle Name - Information Dissemination </label>
                        
                       <select class="form-control" id="work_bundle<?php echo $w; ?>" name="work_bundle[]">
                            <option value="">Select</option>
                            <?php
							$strategy = get_strategy_type($strategy_type);
							$strategy_items = get_Wb_items($strategy);
							while($items=mysql_fetch_array($strategy_items))
							{ ?>
								<option <?php if($work_bundle[$w]==$items[1]){echo 'selected="selected"';} ?> value="<?php echo $items[1]?>"><?php echo $items[1]?></option>
							<?php } ?>
                        </select>
                        <!--
                        <div class="form-group" style="display:none;" id="other_wb_cover<?php echo $w; ?>" >
                            <label></label>
                            <input type="hidden" name="other_wb[]" value="0" id="other_wb<?php echo $w; ?>" />
                            <input type="text" class="form-control" id="other_work_bundle<?php echo $w; ?>" />
                        </div>-->
                      </div>
                      <div class="form-group">
                        <label><span class="text_blue bold">3. </span>Work Bundle Description</label>
                        <textarea class="form-control mar_b20" name="description[]"><?php echo $description[$w];?></textarea>
                      </div>
                      <div class="form-group">
                          <label><span class="text_blue bold">4. </span>Responsible Parties</label>
                          <input type="text" class="form-control" name="responsibilities_parties[]" value="<?php echo $responsibilities_parties[$w];?>">
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-xs-12">
                    <div class="form">
                        <div class="form-group">
                          <label><span class="text_blue bold">5. </span>Target Audience</label>
                          <select class="form-control" name="target_audience[]" onchange="enable_others(<?php echo $w; ?>,this.value)">
                          <option value="">Select</option>
                          <option <?php if($target_audience[$w]=='Project Population'){echo 'selected="selected"';} ?>>Project Population</option>
                          <option <?php if($target_audience[$w]=='School Admin'){echo 'selected="selected"';} ?>>School Admin</option>
                          <option <?php if($target_audience[$w]=='Local Board Members'){echo 'selected="selected"';} ?>>Local Board Members</option>
                          <option <?php if($target_audience[$w]=='State Board Members'){echo 'selected="selected"';} ?>>State Board Members</option>
                          <option <?php if($target_audience[$w]=='Community Key Stakeholdres'){echo 'selected="selected"';} ?>>Community Key Stakeholdres</option>
                          <option <?php if($target_audience[$w]=='Partnering Agencies'){echo 'selected="selected"';} ?>>Partnering Agencies</option>
                          <option <?php if($target_audience[$w]=='Law Enforcement'){echo 'selected="selected"';} ?>>Law Enforcement</option>
                          <option <?php if($target_audience[$w]=='Public Health'){echo 'selected="selected"';} ?>>Public Health</option>
                          <option <?php if($target_audience[$w]=='Others'){echo 'selected="selected"';} ?> value="Others">Others(Describe.)</option>
                          </select>                          
                        </div>
                        
                        <div class="form-group">
                        <input id="other_target_audience<?php echo $w; ?>" <?php if($target_audience[$w]!='Others'){ echo 'style="display: none;"';}?>  type="text" class="form-control"  name="other_target_audience[]" placeholder="Others Target Audience" value="<?php echo $other_target_audience[$w] ?>" />
                        </div>
                        
                        <div class="form-group">
                          <label class="ele_bl"><span class="text_blue bold">6. </span>Is this Work Bundle ongoing?</label>
                          <label class="checkbox_normal mar_r15">
                            <input class="wb_ongoing<?php echo $w; ?>" onclick="enable_enddate(<?php echo $w; ?>)" type="radio" name="end_status[<?php echo $w; ?>]" value="Yes" <?php if($end_status[$w]=='Yes'){echo 'checked="checked"';} ?>>
                            <span class="custom-icon radio-icon"></span>Yes
                          </label>
                          <label class="checkbox_normal">
                            <input class="wb_ongoing<?php echo $w; ?>" onclick="enable_enddate(<?php echo $w; ?>)" type="radio" name="end_status[<?php echo $w; ?>]" value="No" <?php if($end_status[$w]=='No'){echo 'checked="checked"';} ?>>
                            <span class="custom-icon radio-icon"></span>No
                          </label>  
                        </div>
                        
                        <div class="form-group" style="<?php if($end_status[$w]=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?> " id="ongoing_yes_cover<?php echo $w; ?>">
                          <label><span class="text_blue bold">6.a </span>Brief explanation</label>
                          <input type="text" class="form-control" name="ongoing_explain[]" value="<?php echo $ongoing_explain[$w];?>">
                        </div>
                        
                        <div class="row" style="<?php if($end_status[$w]=='No'){echo 'display: block;';}else{echo 'display: none;';} ?> " id="end_date_cover<?php echo $w; ?>" >                                           
                        <div class="col-md-6 form-group">
                          <label><span class="text_blue bold">6.b </span>Projected Start Date</label>
                          <div class="input-group date form_date" data-date-format="mm/dd/yyyy">
                            <input type="text" readonly="" class="form-control" name="start_date[]" value="<?php echo $start_date[$w];?>">
        					<span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span>
                          </div>                          
                        </div>
                        <div class="form-group col-md-6">
                          <label><span class="text_blue bold"></span>Projected Start Date</label>
                          <div class="input-group date form_date" data-date-format="mm/dd/yyyy">
                            <input type="text" readonly="" class="form-control" name="end_date[]" value="<?php echo $end_date[$w];?>">
        					<span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span>
                          </div>                          
                        </div>					      
                        </div>  
                        <div class="form-group">
                          <label class="ele_bl"><span class="text_blue bold">7. </span>Would you like Training or TA on this Work Bundle?</label>
                          <label class="checkbox_normal mar_r15">
                            <input onclick="like_training(<?php echo $w; ?>,1)" type="radio" name="like_training[<?php echo $w; ?>]" value="Yes" <?php if($like_training[$w]=='Yes'){echo 'checked="checked"';} ?>>
                            <span class="custom-icon radio-icon"></span>Yes
                          </label>
                          <label class="checkbox_normal">
                            <input onclick="like_training(<?php echo $w; ?>,0)" type="radio" name="like_training[<?php echo $w; ?>]" value="No" <?php if($like_training[$w]=='No'){echo 'checked="checked"';} ?>>
                            <span class="custom-icon radio-icon"></span>No
                          </label>  
                        </div>
                        <div class="form-group tell_more<?php echo $w; ?>" style="display:<?php if($like_training[$w]=='Yes'){echo 'block'; }else{echo 'none';}?>;">
                          <label>Tell us more</label>
                          <textarea class="form-control mar_b20" name="about_training[]"><?php echo $about_training[$w];?></textarea>                
                        </div>  
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
				<div class="form">
    				<div class="form-group">    					
                        <label><span class="text_blue bold">8. </span>Action Steps</label>
                        <div id="action_steps<?php echo $w; ?>" class="action_steps form col-md-12">
                        <?php for($a=0;$a<count($activities[$w]);$a++) {?>
                          <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                              <input type="text" class="form-control" placeholder="Action Step" name="activities[<?php echo $w?>][]" value="<?php echo $activities[$w][$a];?>" >
                            </div>
                          </div>
                          <?php }?>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-md-12 form">
                   <input type="hidden" id="add_action_count<?php echo $w; ?>" value="1" /> 
                   <button type="button" class="add_button mar_b15" onclick="add_action_steps(<?php echo $w; ?>)"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add Action Steps</button>
                </div>
          </div>
        <?php }}
        else{
        ?>
          <div class="row portfolio_workbundle pad_t20">
                      <div class="form-group col-md-12">
                        <label class="add_button ele_bl text_white pad10 mar0 "><span class="bold">1. </span><?php echo $intervention_community; ?></label>
                      </div>
               <div class="col-md-6 col-xs-12">
                    <div class="form">
                      
                      <div class="form-group">
                        <label><span class="text_blue bold">2. </span>Work Bundle Name </label>
                        <select class="form-control" id="work_bundle<?php echo $w; ?>" name="work_bundle[]">
                            <option value="">Select</option>
                            <?php
							$strategy = get_strategy_type($strategy_type);
							$strategy_items = get_Wb_items($strategy);
							while($items=mysql_fetch_array($strategy_items))
							{ ?>
								<option <?php if($work_bundle[$w]==$items[1]){echo 'selected="selected"';} ?> value="<?php echo $items[1]?>"><?php echo $items[1]?></option>
							<?php } ?>
                        </select>
                        <input type="text" class="form-control" name="other_work_bundle[]" id="other_work_bundle<?php echo $w; ?>" style="display:none;" />
                      </div>
                      <div class="form-group">
                        <label><span class="text_blue bold">3. </span>Work Bundle Description</label>
                        <textarea class="form-control mar_b20" name="description[]"></textarea>
                      </div>
                      <div class="form-group">
                          <label><span class="text_blue bold">4. </span>Responsible Parties</label>
                          <input type="text" class="form-control" name="responsibilities_parties[]" value="">
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-xs-12">
                    <div class="form">
                        <div class="form-group">
                          <label><span class="text_blue bold">5. </span>Target Audience</label>
                          <select class="form-control" name="target_audience[]" onchange="enable_others(0,this.value)">
                          <option value="">Select</option>
                          <option>Project Population</option>
                          <option>School Admin</option>
                          <option >Local Board Members</option>
                          <option>State Board Members</option>
                          <option>Community Key Stakeholdres</option>
                          <option >Partnering Agencies</option>
                          <option>Law Enforcement</option>
                          <option >Public Health</option>
                          <option value="Others">Others(Describe.)</option>
                          </select>
                        </div>
                        <div class="form-group">
                        <input id="other_target_audience0" style="display: none;"  type="text" class="form-control"  name="other_target_audience[]" placeholder="Others Target Audience" />
                        </div>
                        <div class="form-group">
                          <label class="ele_bl"><span class="text_blue bold">6. </span>Is this Work Bundle ongoing?</label>
                          <label class="checkbox_normal mar_r15">
                            <input class="wb_ongoing<?php echo $w; ?>" onclick="enable_enddate(<?php echo $w; ?>)" type="radio" name="end_status[<?php echo $w; ?>]" value="Yes" <?php if($end_status[$w]=='Yes'){echo 'checked="checked"';} ?>>
                            <span class="custom-icon radio-icon"></span>Yes
                          </label>
                          <label class="checkbox_normal">
                            <input class="wb_ongoing<?php echo $w; ?>" onclick="enable_enddate(<?php echo $w; ?>)" type="radio" name="end_status[<?php echo $w; ?>]" value="No" <?php if($end_status[$w]=='No'){echo 'checked="checked"';} ?>>
                            <span class="custom-icon radio-icon"></span>No
                          </label>  
                        </div>
                        
                        <div class="form-group" style="<?php if($end_status[$w]=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?> " id="ongoing_yes_cover<?php echo $w; ?>">
                          <label><span class="text_blue bold">6.a </span>Brief explanation</label>
                          <input type="text" class="form-control" name="ongoing_explain[]" value="<?php echo $ongoing_explain[$w];?>">
                        </div>
                        
                        <div class="row" style="<?php if($end_status[$w]=='No'){echo 'display: block;';}else{echo 'display: none;';} ?> " id="end_date_cover<?php echo $w; ?>" >                                           
                        <div class="col-md-6 form-group">
                          <label><span class="text_blue bold">6.b </span>Projected Start Date</label>
                          <div class="input-group date form_date" data-date-format="mm/dd/yyyy">
                            <input type="text" readonly="" class="form-control" name="start_date[]" value="<?php echo $start_date[$w];?>">
        					<span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span>
                          </div>                          
                        </div>
                        <div class="form-group col-md-6">
                          <label><span class="text_blue bold"></span>Projected Start Date</label>
                          <div class="input-group date form_date" data-date-format="mm/dd/yyyy">
                            <input type="text" readonly="" class="form-control" name="end_date[]" value="<?php echo $end_date[$w];?>">
        					<span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span>
                          </div>                          
                        </div>					      
                        </div> 
                        <div class="form-group">
                          <label class="ele_bl"><span class="text_blue bold">7. </span>Would you like Training or TA on this Work Bundle?</label>
                          <label class="checkbox_normal mar_r15">
                            <input onclick="like_training(0,1)" type="radio" name="like_training[0]" value="Yes" >
                            <span class="custom-icon radio-icon"></span>Yes
                          </label>
                          <label class="checkbox_normal">
                            <input onclick="like_training(0,0)" type="radio" name="like_training[0]" value="No" >
                            <span class="custom-icon radio-icon"></span>No
                          </label>  
                        </div>
                        <div class="form-group tell_more0" style="display: none;">
                          <label>Tell us more</label>
                          <textarea class="form-control mar_b20" name="about_training[]"><?php echo $intervention_row['about_training'];?></textarea>                
                        </div>  
                    </div>
                </div>
				<div class="col-md-12 col-xs-12">
    				<div class="form">
        				<div class="form-group">
        				<label><span class="text_blue bold">8. </span>Action Steps</label>
                        <div id="action_steps0" class="action_steps form col-md-12"> 
                          <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                              <input type="text" class="form-control" placeholder="Action Step" name="activities[0][]" >
                            </div>
                          </div>
                          <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                              <input type="text" class="form-control"placeholder="Action Step" name="activities[0][]" >
                            </div>
                          </div>
                        </div>
        				</div>
    				</div>
				</div>
                <div class="col-md-12 form">
                   <input type="hidden" id="add_action_count0" value="1" /> 
                   <button type="button" class="add_button mar_b15" onclick="add_action_steps(0)"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add Action Steps</button>
                </div>
          </div>
          <?php } ?>
        </div>
        <div class="col-xs-12 col-sm-12 text-center form mar_b30">
            <input type="hidden" id="bundle_count" value="<?php echo $work_bundle_count; ?>" />
            <button type="button" class="mar_r15" style="width: 150px;" id="addbundle" onclick="add_new_bundle('<?php echo str_replace("'", "\'", $intervention_community); ?>','<?php echo $strategy_type;?>')">Add New Bundle</button>
            <?php if($_POST['intervention_id']>0){                
                $save_btn=($intervention_row['part_e_save']==0 ? 'savePartE()':'not_save()');                 
            ?>
              <input type="hidden" name="partE_edit" value="<?php echo $_POST['intervention_id'] ?>" />   
            <?php }else{
              $save_btn='savePartE()'; 
            ?>
              <input type="hidden" name="partE_save" value="1" />               
            <?php }?>
                
              <a class="button save_btn" onclick="<?php echo $save_btn; ?>" style="padding: 10px 20px;cursor: pointer;">Save</a>
              <a class="mar_l15 button cancel_btn" onclick="editPart(5)" style="padding: 10px 20px;cursor: pointer;">Edit</a>
              
         </div>
      </form>
      </div>
    </div>
  </div>
  <!-------Part E End---------->
  
  </div>
<script>
$(function () {
    $(".number_only").keydown(function (e) {  
          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
        }
    });  
	$("[data-toggle = 'tooltip']").tooltip();
	$(".profile-icon").tooltip('show');
	setTimeout(function(){
		$(".profile-icon").tooltip('hide');
	},3000);
 });
$(document).ready(function() {
 $('.disabled_input input,.disabled_input select,.disabled_input textarea,.disabled_input button').prop('disabled',true);

 $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
  $('.strategy_select').select2();
});
  $(document).ready(function(){
          $('.resources_select').select2();
        });
</script>               
<script>
var manualUploader = new qq.FineUploader({
    element: document.getElementById('fine-uploader-manual-trigger'),
    template: 'qq-template-manual-trigger',
    request: {
        endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
    },
    deleteFile: {
        enabled: true,
        endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
    },
    chunking: {
        enabled: true,
        concurrent: {
            enabled: true
        },
        success: {
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php?done"
        }
    },
    resume: {
        enabled: true
    },
    retry: {
        enableAuto: true,
        showButton: true
    },
    autoUpload: true,
    debug: true
});

var manualUploader = new qq.FineUploader({
    element: document.getElementById('fine-uploader-manual-trigger1'),
    template: 'qq-template-manual-trigger1',
    request: {
        endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
    },
    deleteFile: {
        enabled: true,
        endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php"
    },
    chunking: {
        enabled: true,
        concurrent: {
            enabled: true
        },
        success: {
            endpoint: "<?php echo $site_url; ?>/assets/uploader/php-traditional-server/endpoint.php?done"
        }
    },
    resume: {
        enabled: true
    },
    retry: {
        enableAuto: true,
        showButton: true
    },
    autoUpload: true,
    debug: true
});

qq(document.getElementById("trigger-upload")).attach("click", function() {
    manualUploader.uploadStoredFiles();
});

function add_audience(id){
	
		var MaxInputs       = 5; 
		var c = $('#audience_members_count').val(); 
		if(c <= MaxInputs) 
		{
		c=c+2; //text box increment
		$("#audience_members").append('<div class="row"><div class="col-md-6 "><input class="form-control number_only" name="in_group_com_met[]" value="0" type="text"></div><div class="col-md-6 "><input class="form-control number_only" name="in_group_com_met[]" value="0" type="text"></div></div><br>');
		 
		$('#audience_members_count').val(c);
		
		}
	 
    return false;
 }

</script>
