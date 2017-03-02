<div class="panel-body">
    <input type="hidden" class="contract_num" searchFields="" searchValues="" id="contract_num" name="contract_num" value="<?php echo $tta_details['contract_num']; ?>">
     <div class="form">
      <div class="col-xs-12">
            <div class="form-group row">
              <label class="col-md-6 text-right">Contract Number</label>
              <label class="col-md-6 text_blue ft_24 text-left"><b><?php echo $tta_details['contract_num']; ?></b></label>
            </div>
      </div>
      <hr>
       <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Status</label>
              <?php 
              if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)){
               $selected_name='status' ;
               $hidden_name='status_duplicate';
              }else{
                $selected_name='status_duplicate' ;
               $hidden_name='status';
              } ?>
                <select class="form-control" name="<?php echo $selected_name; ?>" id="status" <?php if(($_SESSION['userrole']==1)||($_SESSION['userrole']==4)){}else{ echo 'disabled';} ?>>
                    <option value="finished" <?php if(isset($_GET['searchValues']) && ($_GET['searchValues']=='finished')) {  echo "selected";  } else if($tta_details['status'] == "finished"){  echo "selected"; }?>>Finished</option>
                    <option value="started" <?php  if(isset($_GET['searchValues']) && ($_GET['searchValues']=='started')) {  echo "selected";  } else if($tta_details['status'] == "started"){  echo "selected"; }?>>Started</option>
                    <option value="pending" <?php  if(isset($_GET['searchValues']) && ($_GET['searchValues']=='pending')) {  echo "selected";  } else if($tta_details['status'] == "pending"){  echo "selected"; }?>>Pending</option>
                </select>
                <input type="hidden" name="<?php echo $hidden_name; ?>" value="<?php echo $tta_details['status']; ?>" />
            </div>
            <div class="form-group">
              <label>Inquiry Type</label>
              <select class="form-control" name="TTA_inquiry_type">
                  <option value="Training" <?php if($tta_details['TTA_inquiry_type']=="Training") { echo "selected"; } else {} ?>>Training</option>
                  <option value="Technical Assistance" <?php if($tta_details['TTA_inquiry_type']=="Technical Assistance") { echo "selected"; } else {} ?>>Technical Assistance </option>
              </select>
              <textarea name="TTA_inquiry_notes" class="form-control mar_t10" placeholder="Write something here"><?php echo $tta_details['TTA_inquiry_notes']; ?></textarea>
            </div>
            <div class="form-group">
              <label>Regarding</label>
              <select class="form-control" data-init-plugin="cs-select" id="regarding" name="regarding">
                  <option value="">Select</option>
                  <option value="Implementation" <?php if($tta_details['regarding']=="Implementation") { echo "selected"; } else {} ?>>Implementation</option>
                  <option value="Capacity" <?php if($tta_details['regarding']=="Capacity") { echo "selected"; } else {} ?>>Capacity</option>
                  <option value="Evaluation" <?php if($tta_details['regarding']=="Evaluation") { echo "selected"; } else {} ?>>Evaluation</option>
                  <option value="Technology" <?php if($tta_details['regarding']=="Technology") { echo "selected"; } else {} ?>>Technology</option>
                  <option value="Other" <?php if($tta_details['regarding']=="Other") { echo "selected"; } else {} ?>>Other</option>
              </select>
            </div>
            <div class="form-group">
              <label>Discovery Notes</label>
              <div class="form">
                <a data-target="#usercomments-quickview" data-toggle="modal" onclick="regarding_comments('<?php echo $tta_details['contract_num']; ?>','<?php echo $tta_details['agency_id']; ?>');" class="button"><span class="pad_lr10"><i class="fa fa-comments"></i> Chat</span></a>
              </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Resource</label>
              <div class="resource_list">
                  <?php
                  $resources=unserialize($tta_details['resources']);
                  ?>
                  <select name="resources[]"  class="form-control resources_select" data-init-plugin="select2" multiple="multiple">
                        <option>Select</option>
                  <?php

                  $query_resources=mysql_query("SELECT document_name,id FROM documents ");

                  while($row1=mysql_fetch_array($query_resources)) {
                      $document_link=$row1['document_name'];
                      $document_arr=explode('/',$document_link);
                      $count_no=count($document_arr)-1;
                      $document_det=explode('.',$document_arr[$count_no]);
                      $return1=$document_det[0].' ('.$document_det[1].')';
                      $return=str_replace('-',' ',$return1);

                              ?> <option value="<?php echo $row1['id'];?>" <?php if(in_array($row1['id'],$resources)) {echo 'selected="selected"';} ?>> <?php echo ucfirst($return);?> </option><?php
                          ?>
                        <?php
                        }
                        ?>
                    </select>
                  <?php
                 echo resources($tta_details['resources']); ?>
              </div>
            </div>
            <?php
            $help_query="SELECT uploadfoldername,uploadfilename,filepath FROM help WHERE contract_num='".$tta_details['contract_num']."'";
            $help_upload = mysql_query($help_query);
            $upload_help = mysql_fetch_array($help_upload);
            $arrayfoldername=unserialize($upload_help['uploadfoldername']);
            $arrayfilename=unserialize($upload_help['uploadfilename']);
            $fileCount = count($arrayfilename);
            ?>
            <div class="form-group">
              <label>Attachments</label>
              <div class="attachment-list">
                <ul>
                    <?php for($i=0;$i<$fileCount;$i++){   if(!empty($arrayfilename[$i])&&!empty($arrayfilename[$i])){?>
                        <li><a target="_blank" href="<?php echo $site_url; ?>/assets/uploader/php-traditional-server/files/<?php echo $arrayfoldername[$i]; ?>/<?php echo $arrayfilename[$i]; ?>" ><?php echo $arrayfilename[$i]; ?></a></li>
                    <?php }}?>
                </ul>
              </div>
             <div id="fine-uploader-manual-trigger_<?php echo $tta_details['contract_num']; ?>"></div>
                <script type="text/template" id="qq-template-manual-trigger_<?php echo $tta_details['contract_num']; ?>">
                    <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
                        <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                        </div>
                        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                            <span class="qq-upload-drop-area-text-selector"></span>
                        </div>
                        <div class="buttons">
                            <div class="qq-upload-button-selector qq-upload-button">
                                <div>Select files</div>
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
              
            </div>
            <div class="form-group">
              <label>Modality</label>
                <?php
                $modality_spilt=explode(', ',$tta_details['modality']);
                ?>
              <div class="modality_list">
                <ul>
                  <li>
                    <label class="mar0">
                        <input type="checkbox" <?php if(in_array("webscreen",$modality_spilt)){ ?>checked="checked"<?php } ?>  value="webscreen" name="modality_web" >
                        <span class="custom-icon checkbox-icon"></span>Web/Screen Share
                    </label>
                  </li>
                  <li>
                    <label class="mar0">
                        <input type="checkbox" value="Correspondence" <?php if(in_array("Correspondence",$modality_spilt)){ ?>checked="checked"<?php } ?>  name="modality_correspondence">
                        <span class="custom-icon checkbox-icon"></span>Phone/Correspondence
                    </label>
                  </li>
                  <li>
                    <label class="mar0">
                        <input type="checkbox" value="Face" <?php if(in_array("Face",$modality_spilt)){ ?>checked="checked"<?php } ?>  name="modality_faceface">
                        <span class="custom-icon checkbox-icon"></span>Face to Face
                    </label>
                  </li>
                  <li>
                    <label class="mar0">
                        <input type="checkbox" value="Other" <?php if(in_array("Other",$modality_spilt)){ ?>checked="checked"<?php } ?> name="modality_other">
                        <span class="custom-icon checkbox-icon"></span>Other
                    </label>
                  </li>
                  <li>
                    <div class="form-group">
                       <input type="text" class="form-control" name="other_email" placeholder="Write something here" value="<?php echo $tta_details['modality_other']  ?>">
                    </div>
                  </li>
                  <li>
                    <label class="mar0">
                        <input type="checkbox" name="modality_combination"  <?php if(in_array("Combination",$modality_spilt)){ ?>checked="checked"<?php } ?>  value="Combination" >
                        <span class="custom-icon checkbox-icon"></span>Combination
                    </label>
                  </li>
                </ul>
              </div>
            </div>
        </div>
      </div>
  </div>
  
<?php
