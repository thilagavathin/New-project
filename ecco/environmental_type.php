<?php 
include_once('config.php');
include_once('strategy_fn.php');
error_reporting(0);
if(isset($_POST['intervention_id'])){ 
    
    $types_of_participants=array();
    $target_population=array();
    $alternative_participants=array();
    
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
    
    $types_of_participants= ($intervention_row['types_of_participants']!=''?unserialize($intervention_row['types_of_participants']):array());
    $target_population= ($intervention_row['target_population']!=''?unserialize($intervention_row['target_population']):array());
    $alternative_participants= ($intervention_row['alternative_participants']!=''?unserialize($intervention_row['alternative_participants']):array());
    $policy_related_activities= ($intervention_row['policy_related_activities']!=''?unserialize($intervention_row['policy_related_activities']):array());
    $environmental_organizations= ($intervention_row['environmental_organizations']!=''?unserialize($intervention_row['environmental_organizations']):array());
    $en_training= ($intervention_row['en_training']!=''?unserialize($intervention_row['en_training']):array());
    $types_of_establishments= ($intervention_row['types_of_establishments']!=''?unserialize($intervention_row['types_of_establishments']):array());
   
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
              <textarea class="form-control" name="addressed_issue" id="addressed_issue"><?php echo $adddr ?></textarea>
            </div>
             <div class="form-group">
              <label><span class="text_blue bold">3. </span>Approved State Priority</label>
              <input type="text" class="form-control" name="approved_state_priority" id="approved_state_priority" value="<?php echo $appstat; ?>">
            </div>
              <div class="form-group">
                <label><span class="text_blue bold">4. </span>Strategy Type</label>
                
                <select class="form-control mar_b20" name="strategy_type" id="strategy_type" onchange="change_servicetype(this.value,'<?php echo $_POST['intervention_id']; ?>','<?php echo $strategy_type;?>')">
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
              <!--
            <div class="form-group">
              <label class=""><span class="text_blue bold">7. </span>Select Intervention Model</label>
              <select class="form-control" name="intervention_model" id="intervention_model">
              <option <?php if($intervention_row['intervention_model']==''){echo 'selected=""';} ?> value="">Select Intervention Model</option>
              </select>
            </div>--> 
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
                    <input type="radio" name="EBP" onclick="hide_show('EBP','evidence_type_cover')" value="Yes" <?php if($intervention_row['EBP']=='Yes'){echo 'checked="checked"';} ?>>
                    <span class="custom-icon radio-icon"></span>Yes
                  </label>
                  <label class="checkbox_normal">
                    <input type="radio" name="EBP" onclick="hide_show('EBP','evidence_type_cover')" value="No" <?php if($intervention_row['EBP']=='No'){echo 'checked="checked"';} ?>>
                    <span class="custom-icon radio-icon"></span>No
                  </label>                  
                </div>
                <div class="form-group" id="evidence_type_cover" style="<?php if($intervention_row['EBP']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>">
                  <label><span class="text_blue bold">3. </span>Strategy Model / Intervention Name</label>                  
                  <select class="form-control strategy_select" id="strategy_model" name="strategy_model" onchange="toogle_select(this.value,'strategy_model_other_cover')">
                  <option value="">Start typing the intervention name</option>
                  <?php    
                    $sql = "select id,strategy_name from strategy_model where strategy_type='4'";
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
                <h4 class="text_blue">Policy Enactment, Establishment, or Implementation</h4>
				<div class="form-group">
				<label class="ele_bl"><span class="text_blue bold">4. </span>Does this environmental strategy include policy enactment, establishment, or implementation?</label>
					<label class="checkbox_normal mar_r15">
                      <input type="radio" name="environment_strategy" onchange="hide_show('environment_strategy','en_alternative_activities_cover')" value="Yes" <?php if($intervention_row['environment_strategy']=='Yes'){echo 'checked="checked"';} ?>>
                      <span class="custom-icon radio-icon"></span>Yes
                    </label>
                    <label class="checkbox_normal">
                      <input type="radio" name="environment_strategy" onchange="hide_show('environment_strategy','en_alternative_activities_cover')" value="No" <?php if($intervention_row['environment_strategy']=='No'){echo 'checked="checked"';} ?>>
                      <span class="custom-icon radio-icon"></span>No
                    </label>
				</div>
                <div style="<?php if($intervention_row['environment_strategy']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_alternative_activities_cover">
				<div class="form-group">  
                  <label class="ele_bl">
                    <label class="ele_bl"><span class="text_blue bold">5. </span>Which of the following policy-related activities will you work to implement for this environmental strategy this year?(Select all that apply)
                    <span class="info-badge2 text-lowercase mar_l10" onclick="info_toggle('info_policy_related_activities')" data-toggle ="tooltip" data-placement ="left" title="Click to view Info">i</span></label>
                    <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 info_policy_related_activities" style="display: none;">
    				  <div class="custom-blockquote mar_b20">
    					<p class="mar0">Include only those activities specific to this specific environmental strategy. Do not include activities conducted for other environmental strategies that you have listed as separate interventions .</p>
    				  </div>
    			     </div>
                     </div>
                    </label>  
                  <select class="form-control resources_select" multiple="" name="policy_related_activities[]" id="policy_related_activities" onchange="toggle_multiselect(this.value,'policy_related_activities','policy_related_activities_other_cover')">
					<option <?php if(in_array('Work to educate key stakeholders to enact policies related to retail access (e.g., liquor outlet regulations on location, density, hours and zoning; keg registration; prescription drug monitoring programs)',$policy_related_activities)){echo 'selected="selected"';} ?>>Work to educate key stakeholders to enact policies related to retail access (e.g., liquor outlet regulations on location, density, hours and zoning; keg registration; prescription drug monitoring programs)</option>
					<option <?php if(in_array('Work to educate key policy stakeholders to enact policies related to social access (e.g., parental hosting laws, prescription drug take back policies)',$policy_related_activities)){echo 'selected="selected"';} ?> >Work to educate key policy stakeholders to enact policies related to social access (e.g., parental hosting laws, prescription drug take back policies)</option>
					<option <?php if(in_array('Work to educate key policy stakeholders to enact policies related to consumption of substances in public places (e.g., open container laws)',$policy_related_activities)){echo 'selected="selected"';} ?> >Workto educate key policy stakeholders to enact policies related to consumption of substances in public places (e.g., open container laws)</option>
					<option <?php if(in_array('Work to educate key policy stakeholders to enact regulations on alcohol or prescription drug advertising and billboard placements in the community',$policy_related_activities)){echo 'selected="selected"';} ?> >Work to educate key policy stakeholders to enact regulations on alcohol or prescription drug advertising and billboard placements in the community</option>
					<option <?php if(in_array('Work to educate key policy stakeholders to establish school, workplace, or organizational policies related to substance use (e.g., drug/alcohol-free school zones, workplace use policies, recreation league policies)',$policy_related_activities)){echo 'selected="selected"';} ?> >Work to educate key policy stakeholders to establish school, workplace, or organizational policies related to substance use (e.g., drug/alcohol-free school zones, workplace use policies, recreation league policies)</option>
					<option <?php if(in_array('Work to educate key policy stakeholders to enact policies to reduce the problems/consequences associated with substance abuse (e.g., crime, driving under the influence)',$policy_related_activities)){echo 'selected="selected"';} ?> >Work to educate key policy stakeholders to enact policies to reduce the problems/consequences associated with substance abuse (e.g., crime, driving under the influence)</option>
					<option <?php if(in_array('Other',$policy_related_activities)){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
				  </select>
				  
				  <div class="form-group" style="<?php if(in_array('Other',$policy_related_activities)){echo 'display: block;';}else{echo 'display: none;';} ?>" id="policy_related_activities_other_cover" >
                        <label></label>
                        <input type="text" placeholder="Describe" class="form-control" name="policy_related_activities_other" id="policy_related_activities_text" value="<?php echo $intervention_row['policy_related_activities_other']; ?>" />
                    </div>
				</div>
				
				<div class="form-group">
				<label><span class="text_blue bold">6. </span>For this environmental strategy, select the organizations and/or agencies you plan to engage or partner with to enact, establish or implement policy (Select all that apply)</label>
					<select class="form-control resources_select" multiple="" name="environmental_organizations[]" id="environmental_organizations" onchange="toggle_multiselect(this.value,'environmental_organizations','environmental_organizations_other_cover')">
					  <option <?php if(in_array('Local school district',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Local school district">Local school district</option>
					  <option <?php if(in_array('Individual school(s)',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Individual school(s)">Individual school(s)</option>
					  <option <?php if(in_array('Alcohol retail merchants',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Alcohol retail merchants">Alcohol retail merchants</option>
					  <option <?php if(in_array('Elected officials',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Elected officials">Elected officials</option>
					  <option <?php if(in_array('Law enforcement',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Law enforcement">Law enforcement</option>
					  <option <?php if(in_array('Liquor licensing board',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Liquor licensing board">Liquor licensing board</option>
					  <option <?php if(in_array('Neighborhood planning units (NPU)',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Neighborhood planning units (NPU)">Neighborhood planning units (NPU)</option>
					  <option <?php if(in_array('Local community group(s)/organization(s)',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Local community group(s)/organization(s)">Local community group(s)/organization(s)</option>
					  <option <?php if(in_array('Other',$environmental_organizations)) {echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
					</select>
					
					<div class="form-group" style="<?php if(in_array('Other',$environmental_organizations)){echo 'display: block;';}else{echo 'display: none;';} ?>" id="environmental_organizations_other_cover" >
                        <label></label>                        
                        <input type="text" placeholder="Describe" name="environmental_organizations_other" class="form-control" id="environmental_organizations_other_text" value="<?php echo $intervention_row['environmental_organizations_other']; ?>" />
                    </div>
				</div>
                </div>
				<h4 class="text_blue">Training of Environmental Influencers</h4>
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">7. </span>Does this environmental strategy include the training of environmental influencers-that is, those in  positions to affect substance abuse through policy, enforcement, communication, and so on (e.g. law enforcement officers, beverage servers, alcohol merchants, health care professionals, school employees)?</label>
					 <label class="checkbox_normal mar_r15">
                      <input type="radio" name="en_alternative_activities" onchange="hide_show('en_alternative_activities','en_enforcement_efforts_cover')" value="Yes" <?php if($intervention_row['en_alternative_activities']=='Yes'){echo 'checked="checked"';} ?>>
                      <span class="custom-icon radio-icon"></span>Yes
                    </label>
                    <label class="checkbox_normal">
                      <input type="radio" name="en_alternative_activities" onchange="hide_show('en_alternative_activities','en_enforcement_efforts_cover')" value="No" <?php if($intervention_row['en_alternative_activities']=='No'){echo 'checked="checked"';} ?>>
                      <span class="custom-icon radio-icon"></span>No
                    </label>
				</div>
				<div style="<?php if($intervention_row['en_alternative_activities']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_enforcement_efforts_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">8. </span>To which group(s) will you provide training to affect substance abuse through policy, enforcement, communication, and so on this year?(Select all that apply)</label>
					   <select class="form-control resources_select" multiple="" name="en_training[]" id="en_training" onchange="toggle_multiselect(this.value,'en_training','en_training_other_cover')">
						  <option <?php if(in_array('Law enforcement',$en_training)) {echo 'selected="selected"';} ?> value="Law enforcement">Law enforcement</option>
						  <option <?php if(in_array('Beverage servers',$en_training)) {echo 'selected="selected"';} ?> value="Beverage servers">Beverage servers</option>
						  <option <?php if(in_array('Alcohol merchants',$en_training)) {echo 'selected="selected"';} ?> value="Alcohol merchants">Alcohol merchants</option>
						  <option <?php if(in_array('Health care professionals',$en_training)) {echo 'selected="selected"';} ?> value="Health care professionals">Health care professionals</option>
						  <option <?php if(in_array('School employees or administrators',$en_training)) {echo 'selected="selected"';} ?> value="School employees or administrators">School employees or administrators</option>
						  <option <?php if(in_array('Business owners or employees',$en_training)) {echo 'selected="selected"';} ?> value="Business owners or employees">Business owners or employees</option>
						  <option <?php if(in_array('Service or civic organization employees or administrators',$en_training)) {echo 'selected="selected"';} ?> value="Service or civic organization employees or administrators">Service or civic organization employees or administrators</option>
						  <option <?php if(in_array('Media members',$en_training)) {echo 'selected="selected"';} ?> value="Media members">Media members</option>
						  <option <?php if(in_array('Military personnel',$en_training)) {echo 'selected="selected"';} ?> value="Military personnel">Military personnel</option>
						  <option <?php if(in_array('Other',$en_training)) {echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
						</select>
						
						<div class="form-group" style="<?php if(in_array('Other',$en_training)){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_training_other_cover" >
                        <label></label>                        
                        <input type="text" placeholder="Describe" name="en_training_other" class="form-control" id="en_training_other_other_text" value="<?php echo $intervention_row['en_training_other']; ?>" />
                    </div>
				</div>
				
                <div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">9. </span>Is this a recurring intervention in which the same group of people will be trained over multiple sessions?</label>
                    <label class="checkbox_normal mar_r15">
                      <input type="radio" name="intervention_type" onchange="hide_show('intervention_type','intervention_type_cover')" value="Yes" <?php if($intervention_row['intervention_type']=='Yes'){echo 'checked="checked"';} ?>>
                      <span class="custom-icon radio-icon"></span>Yes
                    </label>
                    <label class="checkbox_normal">
                      <input type="radio" name="intervention_type" onchange="hide_show('intervention_type','intervention_type_cover')" value="No" <?php if($intervention_row['intervention_type']=='No'){echo 'checked="checked"';} ?>>
                      <span class="custom-icon radio-icon"></span>No
                    </label>
                </div>
				<div style="<?php if($intervention_row['intervention_type']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="intervention_type_cover"   >
            	<div class="form-group">
                <label class="ele_bl"><span class="text_blue bold">9a. </span>Average number of sessions per group?</label>
					<select class="form-control" name="sessions">
					<option value="">Select</option>
						<?php for($j=1;$j<=40;$j++){?>
							<option <?php if($intervention_row['sessions']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					</select>    				
                </div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">9b. </span>Select frequency of sessions per group</label>
                    <select class="form-control" name="frequency" id="frequency_of_session" onchange="toogle_select(this.value,'frequency_of_session_cover')">
                    <option  value="">Select</option>
					<option <?php if($intervention_row['frequency']=='1-2 Times per week'){echo 'selected="selected"';} ?> value="1-2 Times per week">1-2 Times per week</option>
					<option <?php if($intervention_row['frequency']=='3-4 Times per week'){echo 'selected="selected"';} ?> value="3-4 Times per week">3-4 Times per week</option>
					<option <?php if($intervention_row['frequency']=='5-6 Times per week'){echo 'selected="selected"';} ?> value="5-6 Times per week">5-6 Times per week</option>
					<option <?php if($intervention_row['frequency']=='Biweekly (once every 2 weeks)'){echo 'selected="selected"';} ?> value="Biweekly (once every 2 weeks)">Biweekly (once every 2 weeks)</option>
					<option <?php if($intervention_row['frequency']=='Monthly'){echo 'selected="selected"';} ?> value="Monthly">Monthly</option>
					<option <?php if($intervention_row['frequency']=='Other'){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
                    
                    </select>
					<div class="form-group" style="<?php if($intervention_row['frequency']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="frequency_of_session_cover" >
                        <label></label>
                        <input type="text" placeholder="Describe" name="frequency_other" class="form-control" id="frequency_other_text" value="<?php echo $intervention_row['frequency_other']; ?>" />
                    </div>
                </div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">9c. </span>What is the average length of each session (in hours) that will be offered?</label>
						<select class="form-control" name="time_unit">
                            <option value="">Select</option>
                            <?php for($j=0;$j<9;$j++){?>
                                <option <?php if($intervention_row['time_unit']==$j.'.5'){echo 'selected="selected"';} ?> value="<?php echo $j.'.5'; ?>"><?php echo $j.'.5'; ?></option>
                                <option <?php if($intervention_row['time_unit']==($j+1).'.0'){echo 'selected="selected"';} ?> value="<?php echo ($j+1).'.0'; ?>"><?php echo ($j+1).'.0'; ?></option> 
                            <?php }?>
                        </select>
				</div>
                  
                </div>	
			    <div class="form-group">
				<label class="ele_bl"><span class="text_blue bold">10. </span>Will the training of environmental influencers be implemented in a series of cycles in which a new group of participants is served on a regular schedule, such as a new school year?</label>
				<label class="checkbox_normal mar_r15 mar_t5">
					<input type="radio" name="cycles_type" onchange="hide_show('cycles_type','cycle_type_cover')" value="Yes" <?php if($intervention_row['cycles_type']=='Yes'){echo 'checked="checked"';} ?>>
					<span class="custom-icon radio-icon"></span>Yes
                </label>
                <label class="checkbox_normal mar_t5"> 
					<input type="radio" name="cycles_type" onchange="hide_show('cycles_type','cycle_type_cover')" value="No" <?php if($intervention_row['cycles_type']=='No'){echo 'checked="checked"';} ?>>
					<span class="custom-icon radio-icon"></span>No
				</label>
				</div>
                
                <div class="form-group" style="<?php if($intervention_row['cycles_type']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="cycle_type_cover"   >
                    <label class="ele_bl"><span class="text_blue bold">10a. </span>Number of cycles to be implemented this year?</label>
    				  <select class="form-control" name="cycles">
                        <option value="">Select</option>
                        <?php for($j=2;$j<=20;$j++){?>
                            <option <?php if($intervention_row['cycles']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
                           
                        <?php }?>
                    </select>					
				
                </div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">11. </span>What are the formats of the training of environmental influencers you will be implementing? (Select all that apply)</label>
						<select class="form-control" name="prevention_education_intervention" id="prevention_education_intervention" onchange="toogle_select(this.value,'prevention_education_intervention_other_cover')">
						  <!-- <option <?php if(in_array('Individual',$prevention_education_intervention)) {echo 'selected="selected"';} ?> value="Individual">Individual</option>
						  <option <?php if(in_array('Small group (2-9)',$prevention_education_intervention)) {echo 'selected="selected"';} ?> value="Small group (2-9)">Small group (2-9)</option>
						  <option <?php if(in_array('Large group (10-49)',$prevention_education_intervention)) {echo 'selected="selected"';} ?> value="Large group (10-49)">Large group (10-49)</option>
						  <option <?php if(in_array('Extra-large group (50+)',$prevention_education_intervention)) {echo 'selected="selected"';} ?> value="Extra-large group (50+)">Extra-large group (50+)</option>
						  <option <?php if(in_array('Web-based',$prevention_education_intervention)) {echo 'selected="selected"';} ?> value="Web-based">Web-based</option>
						  <option <?php if(in_array('Other',$prevention_education_intervention)) {echo 'selected="selected"';} ?> value="Other">Other</option> -->
						  <option value="">Select</option>
                          <option <?php if($intervention_row['prevention_education_intervention']=='Individual'){echo 'selected="selected"';} ?> value="Individual">Individual</option>
                          <option <?php if($intervention_row['prevention_education_intervention']=='Small group (2-9)'){echo 'selected="selected"';} ?> value="Small group (2-9)">Small group (2-9)</option>
                          <option <?php if($intervention_row['prevention_education_intervention']=='Large group (10-49)'){echo 'selected="selected"';} ?> value="Large group (10-49)">Large group (10-49)</option>
                          <option <?php if($intervention_row['prevention_education_intervention']=='Extra-large group (50+)'){echo 'selected="selected"';} ?> value="Extra-large group (50+)">Extra-large group (50+)</option>
                          <option <?php if($intervention_row['prevention_education_intervention']=='Web-based'){echo 'selected="selected"';} ?> value="Web-based">Web-based</option>
                          <option <?php if($intervention_row['prevention_education_intervention']=='Other'){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
                          
                       </select>
                          <div class="form-group" style="<?php if($intervention_row['prevention_education_intervention']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="prevention_education_intervention_other_cover" >
                            <label></label>
                            <input type="text" placeholder="Describe" class="form-control" name="prevention_education_intervention_other" value="<?php echo $intervention_row['prevention_education_intervention_other'];  ?>" id="prevention_education_intervention_other_text" />
                          </div>
				</div>				
				</div>
				<h4 class="text_blue">Enforcement</h4>
				<div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">12. </span>Does this environmental strategy include enforcement efforts (e.g., compliance checks, sobriety checkpoints, neighborhood surveillance, prescription drug monitoring)?</label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_enforcement_efforts" onchange="hide_show('en_enforcement_efforts','en_sobriety_checks_cover')" value="Yes" <?php if($intervention_row['en_enforcement_efforts']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_enforcement_efforts" onchange="hide_show('en_enforcement_efforts','en_sobriety_checks_cover')" value="No" <?php if($intervention_row['en_enforcement_efforts']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				<div style="<?php if($intervention_row['en_enforcement_efforts']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_sobriety_checks_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">13. </span>Does this environmental strategy include conducting compliance checks that target merchants who sell alcohol to minors?</label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_target_merchants" onchange="hide_show('en_target_merchants','en_law_checks_cover')" value="Yes" <?php if($intervention_row['en_target_merchants']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_target_merchants" onchange="hide_show('en_target_merchants','en_law_checks_cover')" value="No" <?php if($intervention_row['en_target_merchants']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				<div style="<?php if($intervention_row['en_target_merchants']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_law_checks_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">14. </span>Will you be partnering with law enforcement to conduct compliance checks?</label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_law_checks" onchange="hide_show('en_law_checks','law_enforcement_cover')" value="Yes" <?php if($intervention_row['en_law_checks']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_law_checks" onchange="hide_show('en_law_checks','law_enforcement_cover')" value="No" <?php if($intervention_row['en_law_checks']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>				
				<div class="form-group" style="<?php if($intervention_row['en_law_checks']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="law_enforcement_cover">
				<label class="ele_bl"><span class="text_blue bold">14a. </span>With how many law enforcement agencies will you partner to conduct compliance checks?</label>
				    <select class="form-control" name="law_enforcement">
						<?php for($j=1;$j<=10;$j++){?>
                                <option <?php if($intervention_row['law_enforcement']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					</select>
				</div>
				<div class="form-group">
				<label class="ele_bl"><span class="text_blue bold">15. </span>What types of establishments will you be targeting for compliance checks?(Select all that apply)</label>
				  <select class="form-control resources_select" name="types_of_establishments[]" multiple="" id="types_of_establishments">
				    <option <?php if(in_array('On-premises alcohol retailers',$types_of_establishments)) {echo 'selected="selected"';} ?> value="On-premises alcohol retailers">On-premises alcohol retailers</option>
					<option <?php if(in_array('Off-premises alcohol retailers',$types_of_establishments)) {echo 'selected="selected"';} ?> value="Off-premises alcohol retailers">Off-premises alcohol retailers</option>
				  </select>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">16. </span>What is the total number of alcohol retailers located in the community you are targeting based on type? For example, if you are only targeting off-premises retailers, provide the number of off-premises retailers in the community you are targeting. If you are target off-premises and on-premises retailer, list the total number of on-premises and off-premises retailers in your target community.</label>
                    <!--<div class="col-md-12 col-sm-12 col-xs-12 info_number_of_alcohol" style="display: none;">
    				  <div class="custom-blockquote mar_b20">
    					<p class="mar0">For example, if you are only targeting on-premises alcohol retailers than list the total number of on-premises retailers in your target area. If you are conducting compliance checks at on-premises and off-premises retail outlets, list the total combined number of retail outlets in your target area.</p>
    				  </div>
    			     </div>-->
					<select class="form-control" name="number_of_alcohol">
						<?php 
                        for($j=0;$j<100;$j+=10){
						$range_opt=$j.' - '.($j+10);
                        ?>
                             <option <?php if($intervention_row['number_of_alcohol']==$range_opt){echo 'selected="selected"';} ?> value="<?php echo $range_opt; ?>"><?php echo $range_opt; ?></option>
						<?php }?>
					</select>
				</div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">17. </span>What is the total number of retailers at which compliance checks will be implemented?</label>
					<select class="form-control" name="number_of_retailers">
                        <?php 
                        for($j=0;$j<100;$j+=10){
						$range_opt=$j.' - '.($j+10);
                        ?>
                             <option <?php if($intervention_row['number_of_retailers']==$range_opt){echo 'selected="selected"';} ?> value="<?php echo $range_opt; ?>"><?php echo $range_opt; ?></option>
						<?php }?>
					</select>
				</div>
                </div>
                <div class="form-group" >
                    <label class="ele_bl"><span class="text_blue bold">18. </span>How many compliance checks will be conducted for each retailer this year?</label>
					<select class="form-control" name="number_of_compliance">
						<?php for($j=1;$j<=12;$j++){?>
                                <option <?php if($intervention_row['number_of_compliance']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					</select>
				</div>
                </div>
                </div>
                </div>
                
				<div class="col-md-6">
				<div class="form">				               		
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19. </span>Does this environmental strategy include establishing sobriety checkpoints?</label>
					   <label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_sobriety_checks" onchange="hide_show('en_sobriety_checks','number_of_law_enforcement_cover')" value="Yes" <?php if($intervention_row['en_sobriety_checks']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_sobriety_checks" onchange="hide_show('en_sobriety_checks','number_of_law_enforcement_cover')" value="No" <?php if($intervention_row['en_sobriety_checks']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				<div style="<?php if($intervention_row['en_sobriety_checks']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="number_of_law_enforcement_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19a. </span>With how many law enforcement agencies will you partner to conduct sobriety checkpoints?</label>
					   <select class="form-control" name="number_of_law_enforcement">
						<?php for($j=1;$j<=10;$j++){?>
                                <option <?php if($intervention_row['number_of_law_enforcement']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					</select>
				</div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19b. </span>Number of sobriety checkpoints to be established this year?</label>
					   <select class="form-control" name="number_of_sobriety">
						<?php for($j=1;$j<=10;$j++){?>
                                <option <?php if($intervention_row['number_of_sobriety']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					  </select>
				</div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">19c. </span>How frequently will sobriety checks be conducted at established checkpoints?</label>
                      <select class="form-control" name="en_frequently_sobriety" id="en_frequently_sobriety" onchange="toogle_select(this.value,'en_frequently_sobriety_other_cover')">
						<option  value="">Select</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='1-2 Times per week'){echo 'selected="selected"';} ?> value="1-2 Times per week">1-2 Times per week</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='3-4 Times per week'){echo 'selected="selected"';} ?> value="3-4 Times per week">3-4 Times per week</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='5-6 Times per week'){echo 'selected="selected"';} ?> value="5-6 Times per week">5-6 Times per week</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='Biweekly (once every 2 weeks)'){echo 'selected="selected"';} ?> value="Biweekly (once every 2 weeks)">Biweekly (once every 2 weeks)</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='Monthly'){echo 'selected="selected"';} ?> value="Monthly">Monthly</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='Quarterly'){echo 'selected="selected"';} ?> value="Quarterly">Quarterly</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='Twice per year'){echo 'selected="selected"';} ?> value="Twice per year">Twice per year</option>
						<option <?php if($intervention_row['en_frequently_sobriety']=='Other'){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
                    </select>
					
					<div class="form-group" style="<?php if($intervention_row['en_frequently_sobriety']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_frequently_sobriety_other_cover" >
                        <label></label>
                        <input type="text" placeholder="Describe" name="en_frequently_sobriety_other" class="form-control" id="en_frequently_sobriety_other_text" value="<?php echo $intervention_row['en_frequently_sobriety_other']; ?>" />
                    </div>
                </div>
                
                </div>	
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20. </span>For this particular environmental strategy, will you conduct a shoulder tap program?</label>
					<label class="checkbox_normal mar_r15 mar_t5">
						<input type="radio" name="en_shoulder_tap" onchange="hide_show('en_shoulder_tap','en_partner_enforce_cover')" value="Yes" <?php if($intervention_row['en_shoulder_tap']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					</label>
					<label class="checkbox_normal mar_t5"> 
						<input type="radio" name="en_shoulder_tap" onchange="hide_show('en_shoulder_tap','en_partner_enforce_cover')" value="No" <?php if($intervention_row['en_shoulder_tap']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					</label>
			    </div>
				<div style="<?php if($intervention_row['en_shoulder_tap']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_partner_enforce_cover">
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20a. </span>Will you be partnering with law enforcement agencies to conduct the shoulder tap program?</label>
					<label class="checkbox_normal mar_r15 mar_t5">
						<input type="radio" name="en_partner_enforce" onclick="hide_show('en_partner_enforce','number_of_law_agencies_cover')" value="Yes" <?php if($intervention_row['en_partner_enforce']=='Yes'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>Yes
					</label>
					<label class="checkbox_normal mar_t5"> 
						<input type="radio" name="en_partner_enforce" onclick="hide_show('en_partner_enforce','number_of_law_agencies_cover')" value="No" <?php if($intervention_row['en_partner_enforce']=='No'){echo 'checked="checked"';} ?>>
						<span class="custom-icon radio-icon"></span>No
					</label> 
                </div>
				
				<div class="form-group" id="number_of_law_agencies_cover" style="<?php if($intervention_row['en_partner_enforce']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>">
                    <label class="ele_bl"><span class="text_blue bold">20b. </span>With how many different law enforcement agencies will you partner to implement the shoulder tap program?</label>
					  <select class="form-control" name="number_of_law_agencies">
						<?php for($j=1;$j<=10;$j++){?>
                                <option <?php if($intervention_row['number_of_law_agencies']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					  </select>
			    </div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20c. </span>What is the total number of retail outlets located in the target communit(ies) where the shoulder tap program will be implemented?</label>
					  <input type="text" class="form-control" name="number_of_retail_outlets" id="number_of_retail_outlets" value="<?php echo $intervention_row['number_of_retail_outlets']; ?>">
			    </div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20d. </span>What is the total number of retailers you are targeting for your shoulder tap program?</label>
					  <select class="form-control" name="number_of_retailers_tab">
						<?php 
                        for($j=0;$j<100;$j+=10){
						$range_opt=$j.' - '.($j+10);
                        ?>
                             <option <?php if($intervention_row['number_of_retailers_tab']==$range_opt){echo 'selected="selected"';} ?> value="<?php echo $range_opt; ?>"><?php echo $range_opt; ?></option>
						<?php }?>
					</select>
			    </div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20e. </span>How many shoulder tap checks will be conducted at each retail outlet this year?</label>
					  <select class="form-control" name="number_of_shoulder_tab">
						<?php for($j=0;$j<=12;$j++){?>
                                <option <?php if($intervention_row['number_of_shoulder_tab']==$j){echo 'selected="selected"';} ?> value="<?php echo $j; ?>"><?php echo $j; ?></option>
						<?php }?>
					</select>
			    </div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">20f. </span>How frequently will you implement the shoulder tap program at each location?</label>
					  <select class="form-control" name="en_shoulder_location" id="en_shoulder_location" onchange="toogle_select(this.value,'en_shoulder_location_other_cover')">
						<option  value="">Select</option>
						<option <?php if($intervention_row['en_shoulder_location']=='1-2 Times per week'){echo 'selected="selected"';} ?> value="1-2 Times per week">1-2 Times per week</option>
						<option <?php if($intervention_row['en_shoulder_location']=='3-4 Times per week'){echo 'selected="selected"';} ?> value="3-4 Times per week">3-4 Times per week</option>
						<option <?php if($intervention_row['en_shoulder_location']=='5-6 Times per week'){echo 'selected="selected"';} ?> value="5-6 Times per week">5-6 Times per week</option>
						<option <?php if($intervention_row['en_shoulder_location']=='Biweekly (once every 2 weeks)'){echo 'selected="selected"';} ?> value="Biweekly (once every 2 weeks)">Biweekly (once every 2 weeks)</option>
						<option <?php if($intervention_row['en_shoulder_location']=='Monthly'){echo 'selected="selected"';} ?> value="Monthly">Monthly</option>
						<option <?php if($intervention_row['en_shoulder_location']=='Quarterly'){echo 'selected="selected"';} ?> value="Quarterly">Quarterly</option>
						<option <?php if($intervention_row['en_shoulder_location']=='Twice per year'){echo 'selected="selected"';} ?> value="Twice per year">Twice per year</option>
						<option <?php if($intervention_row['en_shoulder_location']=='Once per year'){echo 'selected="selected"';} ?> value="Once per year">Once per year</option>
						<option <?php if($intervention_row['en_shoulder_location']=='Other'){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
                    </select>
					
					<div class="form-group" style="<?php if($intervention_row['en_shoulder_location']=='Other'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_shoulder_location_other_cover" >
                        <label></label>
                        <input type="text" placeholder="Describe" name="en_shoulder_location_other" class="form-control" id="en_shoulder_location_other_text" value="<?php echo $intervention_row['en_shoulder_location_other']; ?>" />
                    </div>
			    </div>
				</div>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">21. </span>For this particular environmental strategy, will you work to implement other enforcement practices or conduct other activities to affect the implementation of enforcement activities?</label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_enforcement_activities" onchange="hide_show('en_enforcement_activities','en_enforcement_activities_cover')" value="Yes" <?php if($intervention_row['en_enforcement_activities']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_enforcement_activities" onchange="hide_show('en_enforcement_activities','en_enforcement_activities_cover')" value="No" <?php if($intervention_row['en_enforcement_activities']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
                <div style="<?php if($intervention_row['en_enforcement_activities']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_enforcement_activities_cover" >
                <div class="form-group" >
                <textarea class="form-control" name="en_enforcement_activities_describe" placeholder="Describe - 3,000 character limit"><?php echo $intervention_row['en_enforcement_activities_describe']; ?></textarea>
                </div>
				
				<h4 class="text_blue">Social Norming Campaign</h4>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">22. </span>For this environmental strategy will you be implementing a social norming campaign?</label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_norming_campaign" onchange="hide_show('en_norming_campaign','en_target_audience_cover')" value="Yes" <?php if($intervention_row['en_norming_campaign']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_norming_campaign" onchange="hide_show('en_norming_campaign','en_target_audience_cover')" value="No" <?php if($intervention_row['en_norming_campaign']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
				<div style="<?php if($intervention_row['en_norming_campaign']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_target_audience_cover" >
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">22a. </span>What is the target audience for your social norms campaign? (Select all that apply)</label>
						<select class="form-control" name="en_target_audience" id="en_target_audience" onchange="toogle_select(this.value,'en_target_audience_other_cover')">
							<option <?php if(in_array('Middle school students', $en_target_audience)){echo 'selected="selected"';} ?> value="Middle school students">Middle school students</option>
							<option <?php if(in_array('High school students', $en_target_audience)){echo 'selected="selected"';} ?> value="High school students">High school students</option>
							<option <?php if(in_array('Parents', $en_target_audience)){echo 'selected="selected"';} ?> value="Parents">Parents</option>
							<option <?php if(in_array('Other', $en_target_audience)){echo 'selected="selected"';} ?> value="Other">Other (Describe.)</option>
						</select>
						
						<div class="form-group" style="<?php if(in_array('Other', $en_target_audience)){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_target_audience_other_cover" >
						<label></label>					
						 <input type="text" placeholder="Describe" name="en_target_audience_other" class="form-control" id="en_target_audience_other_text" value="<?php echo $intervention_row['en_target_audience_other']; ?>" />
						</div>
				</div>
				
				<div class="form-group sitting_location_cover">
                    <label class="ele_bl"><span class="text_blue bold">22b. </span>In which locations will the social norms campaign be implemented?</label>
						<?php
                        $settings_locations=unserialize($intervention_row['settings_locations']);
                        $activity_location_count=count($settings_locations);
                        $settings_locations_count= ($activity_location_count<1 ? 1 : $activity_location_count);
                        for($c=1;$c<=$settings_locations_count;$c++){?>
                    <div class="location<?php echo $c; ?> mar_b20">
                        <?php echo '<label>Setting/Location '.$c.'</label>' ; ?>
                        <textarea class="form-control mar_b20" name="settings_locations[<?php echo $c; ?>][setting]"><?php echo $settings_locations[$c]['setting']; ?></textarea>
                        <div class="row">
                        <span class="col-md-6 col-xs-12 mar_b20"><input type="text" class="form-control" placeholder="Location" name="settings_locations[<?php echo $c; ?>][location]" value="<?php echo $settings_locations[$c]['location']; ?>"/></span>
                        <span class="col-md-6 col-xs-12 mar_b20"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="City/Town" name="settings_locations[<?php echo $c; ?>][city]" value="<?php echo $settings_locations[$c]['city']; ?>"/></span>
                        <span class="col-md-6 col-xs-12"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="Street Address" name="settings_locations[<?php echo $c; ?>][street_address]" value="<?php echo $settings_locations[$c]['street_address']; ?>"/></span>
                        <span class="col-md-6 col-xs-12"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="ZIP" name="settings_locations[<?php echo $c; ?>][zip]" value="<?php echo $settings_locations[$c]['zip']; ?>"/></span>
                        </div>
                    </div>
              <?php }?>
				</div>
                <div class="form-group col-md-12">
                  <input type="hidden" id="add_location_count" value="<?php echo $settings_locations_count; ?>" />  
                  <div class="custom-fileupload">
    				<div class="pull-right"><button type="button" class="add_button" id="add_locations" onclick="add_locations1()"><i class="fa fa-plus-circle"></i> Add Locations</button></div>
    			  </div>
                </div>
                </div>
                </div>
				<h4 class="text_blue">Other Environmental Interventions</h4>
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">23. </span>If this environmental strategy does not fall into the categories of policy, enforcement, training of environmental influencers or social norms campaign, describe the environmental strategy you plan to implement this year.</label>
						<label class="checkbox_normal mar_r15 mar_t5">
							<input type="radio" name="en_strategy_plan" onchange="hide_show('en_strategy_plan','en_strategy_plan_describe_cover')" value="Yes" <?php if($intervention_row['en_strategy_plan']=='Yes'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>Yes
						</label>
						<label class="checkbox_normal mar_t5"> 
							<input type="radio" name="en_strategy_plan" onchange="hide_show('en_strategy_plan','en_strategy_plan_describe_cover')" value="No" <?php if($intervention_row['en_strategy_plan']=='No'){echo 'checked="checked"';} ?>>
							<span class="custom-icon radio-icon"></span>No
						</label>
				</div>
                <div class="form-group" style="<?php if($intervention_row['en_strategy_plan']=='Yes'){echo 'display: block;';}else{echo 'display: none;';} ?>" id="en_strategy_plan_describe_cover" >
                <textarea class="form-control" name="en_strategy_plan_describe" placeholder="Describe - 3,000 character limit"><?php echo $intervention_row['en_strategy_plan_describe']; ?></textarea>
                </div>
				
				<div class="form-group">
                    <label class="ele_bl"><span class="text_blue bold">24. </span>Estimate the total number of individuals that will be reached or affected by your environmental strategy this year.</label>
						<input type="text" class="form-control" name="numbers_served_reached" id="numbers_served_reached" value="<?php echo $intervention_row['numbers_served_reached']; ?>">
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
                        <label><span class="text_blue bold">2. </span>Work Bundle Name - Environmental</label>
                        
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
                          <label><span class="text_blue bold">6a. </span>Brief explanation</label>
                          <input type="text" class="form-control" name="ongoing_explain[]" value="<?php echo $ongoing_explain[$w];?>">
                        </div>
                        
                        <div class="row" style="<?php if($end_status[$w]=='No'){echo 'display: block;';}else{echo 'display: none;';} ?> " id="end_date_cover<?php echo $w; ?>" >                                           
                        <div class="col-md-6 form-group">
                          <label><span class="text_blue bold">6b. </span>Projected Start Date</label>
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
                        <label><span class="text_blue bold">2. </span>Work Bundle Name - Environmental</label>
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


</script>
