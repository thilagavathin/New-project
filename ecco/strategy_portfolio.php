<?php include 'templates/header.php';
if(isset($_REQUEST['agency'])){
    $agency_id= safe_b64decode($_REQUEST['agency']);
    
    $agency_query= "SELECT * FROM agency WHERE id='".$agency_id."' ";
    $agency_data=mysql_query($agency_query);
    $agency_row=mysql_fetch_assoc($agency_data);
    
    $intervention_name_query= "SELECT * FROM interventions WHERE agency_id='".$agency_id."' ORDER BY id ASC ";
    $intervention_name_data=mysql_query($intervention_name_query);
    $inter_name_count=mysql_num_rows($intervention_name_data);
} 
 ?>
 <style>
 .form_date input {
    /*background: #ffffff !important;*/ 
}
.qq-upload-button {
  background: #284fa3 none repeat scroll 0 0 !important;
  border-radius: 5px !important;
  box-shadow: none !important;
  font-weight: bold;
  padding: 10px 0 !important;
}
#overlay { 
  display:block; 
  position:relative; 
  background:#fff; 
}
#img-load { 
  position:relative; 
}
 </style>
  
   
     		<section class="">
	     		  <div class="container">
                  <div class="row">
            		<div class="col-md-12">
            			<ol class="breadcrumb">
            			  <li><a href="systemdashboard.php">Dashboard</a></li>
                          <li><a href="implementation_planning.php">Implementation Planning Dashboard</a></li>
            			  <li class="active">Intervention Mapping </li>              
            			</ol>
            		</div>
            	 </div>
          <!-- page content -->
             <div class="row workbundle">
                <div class="col-md-12">
                  <h1 class="page-title">Intervention Mapping</h1> <!-- page title -->
                    
                </div>
                <div class="col-md-12">
                  <div class="row mar0 portfolio-agency-info-box">
                    <div class="col-md-4 portfolio-title-text">
                       <label>Agency Name</label>
                       <p><?php echo $agency_row['name']; ?><input type="hidden" name="agency_id" id="agency_id" value="<?php echo $agency_id; ?>" /></p>
                    </div>
                    <div class="col-md-4 portfolio-title-text">
                       <label>Region</label>
                       <p><?php echo $agency_row['region']; ?></p>
                    </div>
                    <div class="col-md-4 portfolio-title-text">
                       <label>Listed Users</label>
                       <p><?php echo $agency_row['user_updated']; ?></p>
                    </div>
                  </div>
                </div>
              </div>
              
                <div class="row">
                    <div class="form-group form col-md-4">
                        <button id="add_button" class="add_button mar_t30"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add Intervention</button>
                        <input type="hidden" value="1" name="intervention_count" id="intervention_count"/> 
                    </div>
                </div>
                <div class=" row strategy_list pad0 mar_t10">
                <input type="hidden" name="inter_name_count" id="inter_name_count" value="<?php echo $inter_name_count ?>"  />
                <?php
                $in=1; $p=0;
                
                while($intervention_name=mysql_fetch_assoc($intervention_name_data)){?>
                    <div class="col-md-3" id="cover_inner<?php echo $in ?>" onclick="interventions(<?php echo $in.','.$intervention_name['id']; ?>)">
                      <div class="strategy-box">
                        <div class="form-horizontal">
                          <div class="form-group col-md-12 mar0">
                            <div class="row">
                              <div class="col-md-12">
                             <input type="hidden" id="inter_strategy_type<?php echo $in ?>" name="inter_strategy_type" value="<?php echo $intervention_name['strategy_type'] ?>" />
                             </div>
                             <div class="col-md-12">
                             <input type="text" placeholder="Intervention name" value="<?php echo $intervention_name['intervention_name'] ?>" name="intervention_name" id="intervention_name<?php echo $in ?>" class="form-control mar_b10">
                             </div>
                             <div class="col-md-12">
                      <input type="text" placeholder="Community name" value="<?php echo $intervention_name['intervention_community_name'] ?>" name="intervention_community_name" id="intervention_community_name<?php echo $in ?>" class="form-control mar_b10">
                             </div>
							 
                                <div class="col-md-5 col-xs-12">
                                   <input type="text" placeholder="Zip code" value="<?php echo $intervention_name['intervention_zip_code'] ?>" name="intervention_zip_code" id="intervention_zip_code<?php echo $in ?>" class="form-control mar_b10">
                                </div>
                                <div class="col-md-7 col-xs-12">
                                   <select class="form-control" name="intervention_contract_year" id="intervention_contract_year<?php echo $in ?>">
                                    <option value="">Contract Year</option>
                                    <option <?php if($intervention_name['intervention_contract_year']=='2016-2017'){ echo 'selected=="selected"'; }?> value="2016-2017">2016-2017</option>
                                    <option <?php if($intervention_name['intervention_contract_year']=='2017-2018'){ echo 'selected=="selected"'; }?> value="2017-2018">2017-2018</option>
                                    <option <?php if($intervention_name['intervention_contract_year']=='2018-2019'){ echo 'selected=="selected"'; }?> value="2018-2019">2018-2019</option>
                                    <option <?php if($intervention_name['intervention_contract_year']=='2019-2020'){ echo 'selected=="selected"'; }?> value="2019-2020">2019-2020</option>
                                   </select>
                                </div>
                             </div>
                             <span><a style="cursor: pointer;" id="save_name<?php echo $in ?>" onclick="saveInterventionName(<?php echo $in.','.$intervention_name['id']; ?>)"> Save </a> | <a id="edit_name<?php echo $in ?>" onclick="editInterventionName(<?php echo $in; ?>)" style="cursor: pointer;" >Edit</a></span>
                         </div>
                         <span class="del_inter"><i id="close_inter<?php echo $in ?>" onclick="close_inter(<?php echo $in.','.$intervention_name['id']; ?>)" class="fa fa-times-circle"></i></span>
                        </div>
                      </div>
                    </div>  
					<?php  
                 $in++;  $p++; }?>                  
                </div>
              <div class="row">
                 <div class="col-md-12 mar_t30" id="intervention_parts">
                    
                 </div>
              </div>
            <!-- page content ends -->
          </div>
     		</section>
<?php include 'templates/footer.php'; ?>
	<!-- <div id="overlay" align="center">
  <img src="/assets/images/loading.gif" id="img-load" />
</div> -->

<script src="js/metisMenu.min.js"></script>
<link href="new/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" media="screen">
<script src="new/js/bootstrap-datetimepicker.js" type="text/javascript"></script> 

<link rel="stylesheet" href="pages/css/fine-uploader-new.min.css" type="text/css" />
<script type="text/javascript" src="assets/js/all.fine-uploader.min.js"></script> 
<script type="text/javascript">

$(document).ready(function() {   

var n;
for(n=1;n<=<?php echo $p;?>;n++)
{   
$("#intervention_zip_code"+n).keydown(function (e) { 
	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		return false;
		}
	   }); 
}
								   
/* $("#overlay").css({
  opacity : 0.5,
  top     : $t.offset().top,
  width   : $t.outerWidth(),
  height  : $t.outerHeight()
});
 
$("#img-load").css({
  top  : ($t.height() / 2),
  left : ($t.width() / 2)
});  */

    //document.getElementById("addbundle").disabled=false;
    $('.strategy_list input,.strategy_list select').attr('disabled',true);
 
    var MaxInputs       = 50; 
    var InputsWrapper   = $(".strategy_list"); 
    var AddButton       = $("#add_button");
    var inter_name_count= $('#inter_name_count').val();
    if(inter_name_count>0){var x=inter_name_count;}
    else{var x=0;}
    $(AddButton).click(function (e) 
    {
    if(x <= MaxInputs) 
    {
    x++; //text box increment
	 
    
    $(InputsWrapper).append('<div class="col-md-3" id="cover_inner'+x+'" onclick="interventions('+x+',0)"><div class="strategy-box"><div class="form-horizontal"><div class="form-group col-md-12 mar0"><div class="row"><div class="col-md-12"><input type="hidden" id="inter_strategy_type'+x+'" name="inter_strategy_type"/></div><div class="col-md-12"><input placeholder="Intervention name" type="text" name="intervention_name" id="intervention_name'+x+'" class="form-control mar_b10"></div><div class="col-md-12"><input placeholder="Community name" type="text" name="intervention_community_name" id="intervention_community_name'+x+'" class="form-control mar_b10"></div><div class="col-md-5 col-xs-12"><input placeholder="Zip code" type="text" name="intervention_zip_code" id="intervention_zip_code'+x+'" class="form-control mar_b10"></div><div class="col-md-7 col-xs-12"><select class="form-control" name="intervention_contract_year" id="intervention_contract_year'+x+'"><option value="">Contract Year</option><option >2016-2017</option><option >2017-2018</option><option >2018-2019</option><option >2019-2020</option></select></div></div><span><a style="cursor: pointer;" id="save_name'+x+'" onclick="saveInterventionName('+x+',0)"> Save </a> | <a id="edit_name'+x+'" style="cursor: pointer;" >Edit</a></span></div><span class="del_inter"><i id="close_inter'+x+'" onclick="close_inter('+x+',0)" class="fa fa-times-circle"></i></span></div></div></div>');
   $("#intervention_zip_code"+x).keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
    }
   });
	
     }
    return false;
    });  
    
     
});
          
function editInterventionName(row_num){
    $('#cover_inner'+row_num+' input,#cover_inner'+row_num+' select').attr('disabled',false);
}
function saveInterventionName(row_num,intervention_id){
 var intervention_name=$('#intervention_name'+row_num).val();
 var intervention_community_name=$('#intervention_community_name'+row_num).val();
 var intervention_zip_code=$('#intervention_zip_code'+row_num).val();
 var intervention_contract_year=$('#intervention_contract_year'+row_num).val();
    if(intervention_name==''){
        alert('Please enter intervention name');
        $('#intervention_name'+row_num).focus();
        return false;
    }
    else{
        $.ajax({
            url: "intervention_save.php",
            type: "POST",
            data: '&intervention_name='+intervention_name+'&intervention_community_name='+intervention_community_name+'&intervention_id='+intervention_id+'&intervention_zip_code='+intervention_zip_code+'&intervention_contract_year='+intervention_contract_year+'&save_intervention_name='+1+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
            success: function(data) { 
                if(data==0){sweetAlert("Oops...", "Please Try again", "error");}
                else{                   
                $('#cover_inner'+row_num).attr('onclick','interventions('+row_num+','+data+')');
                $('#close_inter'+row_num).attr('onclick','close_inter('+row_num+','+data+')');
                $('#save_name'+row_num).attr('onclick','saveInterventionName('+row_num+','+data+')');
                $('#edit_name'+row_num).attr('onclick','editInterventionName('+row_num+')');
                $('#cover_inner'+row_num+' input,#cover_inner'+row_num+' select').attr('disabled',true);
        
                interventions(row_num,data);
                sweetAlert("Success...", "Intervention Name saved successfully", "success");
                }   
            }
        });
    }
}
function close_inter(a,intervention_id)
{
if(intervention_id>0){
var x = confirm("Are you sure want to delete this intervention. By deleting this intervention the associated information will be lost This intervention and associated information will need to be re-entered");
if(x == true) {
    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: '&intervention_id='+intervention_id+'&delete_intervention_name='+1,
        success: function(data) { 
            if(data==0){
                sweetAlert("Oops...", "Please Try again", "error");}
            else{  
                sweetAlert("Deleted...", "Intervention deleted successfully", "success");   
                $('#cover_inner'+a).remove(); 
                location.reload();           
                } 
        }
    });
 }
 else {
  return false;
 }
}else{
    $('#cover_inner'+a).remove();
    location.reload();
    }
}


//Add community
function add_community() 
    {
    var MaxInputs       = 50; 
    var x = $('#community_count').val(); 
    if(x <= MaxInputs) 
    {
    x++; //text box increment
    $(".community_cover_box").append('<div class="col-md-4"><div class="communities-box"><h3><span>'+x+'</span><div class="form-group col-md-7 col-xs-7 col-sm-7 col-sm-offset-1 col-xs-offset-1 col-md-offset-1 mar_b0"><input type="text" class="form-control" name="community[name][]" id="community_name'+x+'" placeholder="Community Name"></div><div class="form-group col-md-4 col-xs-4 col-sm-4 mar_b0"><input type="text" class="form-control" name="community[zip][]" id="community_zip'+x+'" placeholder="Zip"></div></h3><div class="communities-box-info"><p>Community Readiness Assessment Date &amp; Score</p><div class="row"><div class="form-group col-md-6 col-xs-6"><label class="">Date</label><div class="input-group date form_date" data-date-format="mm/dd/yyyy"><input class="form-control" type="text" name="community[date][]" readonly><span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span></div></div><div class="form-group col-md-6 col-xs-6"><label>Score</label><select class="form-control" name="community[score][]"><option value="">Select</option><option >1.No Awareness</option><option >2.Denial of Data</option><option>3.Vague Awareness</option><option >4.Preplanning</option><option >5.Preparation</option><option >6.Initiation</option><option >7.Stabilization</option><option >8.Confirmation</option><option >9.Ownership</option></select></div></div></div></div></div>');
    
    $('#community_count').val(x);
    
    }
    
    
    $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
    return false;
    }
	
 function add_rps() 
    {
    
    var MaxInputs       = 20; 
    var c = $('#rps_count').val(); 
    if(c <= MaxInputs) 
    {
    c++; //text box increment
    $(".rps_cover_box").append('<div class="inter_IV'+c+'"><div class="form-group col-md-6"><label>Risk &amp; Protective</label><select class="form-control fw_selectbox" name="IVs[context][]"><option></option></select></div><div class="form-group col-md-6"><div class="form-group cfs_cover'+c+'"><label>Contributing Factors</label><div class="form-group" ><input type="text" class="form-control" name="IVs[cf]['+(c-1)+'][]"></div></div><div class="form-group"><button type="button" class="add_button pull-right add_cfs" onclick="add_cfs('+c+')"><i class="fa fa-plus-circle" aria-hidden="true"></i>CF</button></div></div></div>');
     
    $('#rps_count').val(c);
    
    }
    return false;
    }  
   
 function add_cfs(rp) 
    {
    
    var MaxInputs       = 50; 
    var c = $('#rps_count').val(); 
    if(c <= MaxInputs) 
    {
    c++; //text box increment
    $(".cfs_cover"+rp).append('<div class="form-group" ><input type="text" class="form-control" name="IVs[cf]['+(rp-1)+'][]"></div> ');
     
   
    }
    return false;
    } 
 function add_locations1(){
    var MaxInputs       = 50; 
    var c = $('#add_location_count').val(); 
    if(c <= MaxInputs) 
    {
    c++; //text box increment
    $(".sitting_location_cover").append('<div class="location'+c+' mar_b20"><label>Setting/Location '+c+'</label><textarea class="form-control mar_b20" name="settings_locations['+c+'][setting]"></textarea><div class="row"><span class="col-md-6 col-xs-12 mar_b20"><input type="text" class="form-control" placeholder="Location" name="settings_locations['+c+'][location]"></span><span class="col-md-6 col-xs-12 mar_b20"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="City/Town" name="settings_locations['+c+'][city]"></span><span class="col-md-6 col-xs-12"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="Street Address" name="settings_locations['+c+'][street_address]"></span><span class="col-md-6 col-xs-12"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="ZIP" name="settings_locations['+c+'][zip]"></span></div></div>');
     
    $('#add_location_count').val(c);
    
    }
    return false;
 }
 
 function add_activity_locations(){
    var MaxInputs       = 50; 
    var c = $('#add_location_count').val(); 
    if(c <= MaxInputs) 
    {
    c++; //text box increment
    $(".sitting_location_cover").append('<div class="location'+c+' mar_b20"><label>Setting/Location '+c+'</label><div class="form-group"><input type="text" class="form-control" placeholder="Activity name" name="site_location['+c+'][activity_name]"/></div><div class="form-group"><select class="form-control" name="site_location['+c+'][activity_types]" onchange="activity_types_other1(this.value,'+c+')"><option value="">Select activity type</option><option value="Concert">Concert</option><option value="Festival or fair">Festival or fair</option><option value="Sporting event">Sporting event</option><option value="Picnic">Picnic</option><option value="Drop-in activity">Drop-in activity</option><option value="Web-based gathering">Web-based gathering</option><option value="Other">Other</option></select><div class="form-group" style="display: none;" id="activity_types_other_cover'+c+'"><label></label><input type="text" placeholder="Other" name="site_location['+c+'][activity_types_other]" class="form-control" /></div></div><div class="row"><span class="col-md-6 col-xs-12 mar_b20"><input type="text" class="form-control" placeholder="Location" name="site_location['+c+'][location]"></span><span class="col-md-6 col-xs-12 mar_b20"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="City/Town" name="site_location['+c+'][city]"></span><span class="col-md-6 col-xs-12"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="Street Address" name="site_location['+c+'][street_address]"></span><span class="col-md-6 col-xs-12"><input type="text" class="form-control col-md-6 col-xs-12" placeholder="ZIP" name="site_location['+c+'][zip]"></span></div></div>');
     
    $('#add_location_count').val(c);    
    }
    return false;
 }
 function add_action_steps(bundle){
	
		var MaxInputs       = 50; 
		var c = $('#add_action_count'+bundle).val(); 
		if(c <= MaxInputs) 
		{
		c++; //text box increment
		$("#action_steps"+bundle).append('<div class="col-md-6 col-xs-12"><div class="form-group"><input type="text" class="form-control" placeholder="Action Step" name="activities['+(bundle)+'][]"></div></div><div class="col-md-6 col-xs-12"><div class="form-group"><input type="text" class="form-control" placeholder="Action Step" name="activities['+(bundle)+'][]" ></div></div>');
		 
		$('#add_action_count'+bundle).val(c);
		
		}
	 
    return false;
 }
 function add_new_bundle(name,stype){  
	
  
    var MaxInputs       = 50; 
    var c = $('#bundle_count').val(); 
    if(c <= MaxInputs) 
    {
    c++; //text box increment
	
	$.ajax({
        url: "portfolio_ajax.php",
        type: "POST",
        data: '&intervention_id='+c+'&name='+name+'&stype='+stype,
        success: function(html) {    
                    $("#bundle_cover").append(html);    }
    });
    $('#bundle_count').val(c);    
    }
    $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
   // return false;
 }
      

function interventions(intervention,intervention_id){  
    var strategy_type=$('#inter_strategy_type'+intervention).val();
    var page;
    if(strategy_type=='Prevention Education'){page='interventions.php';}
    else if(strategy_type=='Alternative Drug-Free Activities'){page='drug_free_activities.php';}
    else if(strategy_type=='Environmental'){page='environmental_type.php';}
    else if(strategy_type=='Information Dissemination'){page='information_dissemination.php';}
	else if(strategy_type=='Problem Identification and Referral'){page='problem_identification.php';}
	else if(strategy_type=='Community-Based Processes'){page='community_process.php';}
    else{page='interventions.php';}
    $('.strategy-box').removeClass('active');
    $('#cover_inner'+intervention+' .strategy-box').addClass('active');
    $.ajax({
        url: page,
        type: "POST",
        data: '&intervention_id='+intervention_id+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
        success: function(html) {   
                
                $('#intervention_parts').html(html);
                $('.disabled_input input,.disabled_input select,.disabled_input textarea,.disabled_input button').prop('disabled',true);
                
            }
    });   
    
}
function not_save(){
    sweetAlert("Sorry...", "This part has already been saved.If you would like to edit, please select the edit option.", "error");
}
function edit_save(){
    sweetAlert("Sorry...", "Please select the edit option & edit the feilds", "error");
}

function editPart(part){
    if(part==1){
        var onclick='savePartA()';
        $('#collapse'+part+' #add_community').attr('onclick','add_community()');
    }
    if(part==2){
        var onclick='savePartC()';
        $('#item_uploader_partC').show();
    }
    if(part==3){
        var onclick='savePartB()';
        $('#collapse'+part+' #add_rps').attr('onclick','add_rps()');
    }
    if(part==4)
	{
		var onclick='savePartD()';
		//$('#collapse'+part+' #add_locations').attr('onclick','add_locations()');
		$('#item_uploader_partD').show();		 
    }
    if(part==5){
        var onclick='savePartE()';
    }
    $('#collapse'+part+' input,#collapse'+part+' select,#collapse'+part+' textarea,#collapse'+part+' button').prop('disabled',false);
    $('#collapse'+part+' .save_btn').attr('onclick',onclick);    
}

function savePartA(){
    var partAForm = $('#partAForm').serialize();
    var fillers_name = $("#fillers_name").val();
    var fillers_email = $("#fillers_email").val();
    var fillers_phoneno = $("#fillers_phoneno").val();
    if($("#PC_fillout").is(':checked')){
       var PC_fillout = 1; 
    }else{
        var PC_fillout = 0;    
    }    
    var community_name = $("#community_name").val();
    var manager_name = $("#manager_name").val();
    var manager_email = $("#manager_email").val();
    var manager_phoneno = $("#manager_phoneno").val();
    var agency_id=$("#agency_id").val();
   
    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: partAForm,
        success: function(data) {  
            if(data==0){sweetAlert("Oops...", "Please insert again", "error");}
            else{                
                $("#intervention_id").val(data);
                sweetAlert("Success...", "'Part A' informations are saved successfully", "success");
                $('#collapse1 input,#collapse1 select,#collapse1 textarea').prop('disabled',true);
                $('#collapse1 #add_community').removeAttr('onclick');
                $('#collapse1 .save_btn').attr('onclick','not_save()');
                }
            }
    });
    return false;
}

function savePartB(){
    var intervention_name = $("#intervention_name").val();
    var addressed_issue = $("#addressed_issue").val();
    var approved_state_priority = $("#approved_state_priority").val();      
    var strategy_type = $("#strategy_type").val();
    var IOM_category = $("#IOM_category").val();
    var service_type = $("#service_type").val();
    var intervention = $("#intervention").val();
    var context=$("#context").val();
    var partB_edit=$("#intervention_id").val();
    
    var partBForm = $('#partBForm').serialize();

    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: partBForm+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
        success: function(data) {
            if(data==0){sweetAlert("Oops...", "Please insert again", "error");}
            else
			{
                $("#editPartB").hide();
                sweetAlert("Success...", "'Part B' informations are saved successfully", "success");
                $('#collapse3 input,#collapse3 select,#collapse3 textarea,#collapse3 button').prop('disabled',true);
                $('#collapse3 #add_rps').removeAttr('onclick');
                $('#collapse3 .save_btn').attr('onclick','not_save()');
                //$('#collapse3 .add_cfs').removeAttr('onclick');
				
				$("add_action").val(strategy_type);
				$("#partc").attr('href','#collapse2');
				$('#partc').prop('onclick',null).off('click');
				$("#partd").attr('href','#collapse4');
				$('#partd').prop('onclick',null).off('click'); 
				$("#parte").attr('href','#collapse5');
				$('#parte').prop('onclick',null).off('click');
            }                
                
            }
    });
}

function alert_error_msg()
{
	sweetAlert("Warning!", "You have not Submitted Part B\n By changing this option, intervention forms data will be lost.", "error");
}

function savePartC(){
    var partC_edit=$("#partC_edit").val();
    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: '&partC_edit='+partC_edit,
        success: function(data) {  
            if(data==0){sweetAlert("Oops...", "Please insert again", "error");}
            else{      
                sweetAlert("Success...", "'Part C' Documents are uploaded successfully", "success");
                $('#collapse2 .save_btn').attr('onclick','not_save()');
                $('#item_uploader_partC').hide();
                report_uploads(partC_edit);
                ebp_proff_uploads(partC_edit);
                }
            }
    });
}
function savePartD(){
    var intervention_name = $("#intervention_name").val();
    var partD_edit=$("#partD_edit").val();
    var partDForm = $('#partDForm').serialize();

	var str = $("#add_action").val();
	// alert(str);
	if(str=='' || str=='undefined')
	{
		sweetAlert("Warning!", "You have not Submitted Part B<br> By changing this option, intervention forms data will be lost.", "error");
	}
	else
	{
		$.ajax({
			url: "intervention_save.php",
			type: "POST",
			data: partDForm+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
			success: function(data) {
				if(data==0){
			sweetAlert("Oops...", "Please insert again", "error");}
				else {
					sweetAlert("Success...", "'Part D' informations are saved successfully", "success");
					$('#collapse4 input,#collapse4 select,#collapse4 textarea').prop('disabled',true);
					$('#collapse4 #add_rps').removeAttr('onclick');
					$('#collapse4 .save_btn').attr('onclick','not_save()');
					$('.qq-uploader-selector').hide();
					ebp_proff_uploads(partD_edit);
					report_uploads(partD_edit)
					}
				}
		});
	}
}
function ebp_proff_uploads(inter_id){
    $.ajax({
        url: "ebp_proff_uploads.php",
        type: "POST",
        data: '&intervention_id='+inter_id+'&add_upload='+1,
        success: function(html) {
            $('#ebp_uploads').html(html);
            }
    });
}
function report_uploads(inter_id){
    $.ajax({
        url: "ebp_proff_uploads.php",
        type: "POST",
        data: '&intervention_id='+inter_id+'&report_uploads='+1,
        success: function(html) {
            $('#report_archives').html(html);
            }
    });
}
function remove_uploads(item_no,inter_id){
    var x = confirm("Are you sure want to delete this Iteam permanently.");
    if(x == true) {
        $.ajax({
            url: "ebp_proff_uploads.php",
            type: "POST",
            data: '&intervention_id='+inter_id+'&item_no='+item_no+'&delete_upload='+1,
            success: function(data) {
                if(data){
                   $('#upload_item'+item_no).hide(); 
                   $('#report_box'+item_no).hide(); 
                   sweetAlert("Success...", "Uploaded item removed successfully", "success"); 
                }else{
                   sweetAlert("Oops...", "Please insert again", "error");
                }
                }
        });
    }else{
        return false;
    }
        
}
function savePartE(){
    var partEForm = $('#partEForm').serialize();

    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: partEForm+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
        success: function(data) {  
            if(data==0){sweetAlert("Oops...", "Please insert again", "error");}
            else{       
                sweetAlert("Success...", "'Part E' informations are saved successfully", "success");
                $('#collapse5 input,#collapse5 select,#collapse5 textarea,#collapse5 button').prop('disabled',true);
                $('#collapse5 .save_btn').attr('onclick','not_save()');
                }
                                 
            }
    });
}
function savePartD_drug(){
    var partEForm = $('#partDForm1').serialize();

    $.ajax({
        url: "intervention_save.php",
        type: "POST",
        data: partEForm+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
        success: function(data) {  
            if(data==0){sweetAlert("Oops...", "Please insert again", "error");}
            else{       
                sweetAlert("Success...", "'Part E' informations are saved successfully", "success");
                $('#collapse5 input,#collapse5 select,#collapse5 textarea,#collapse5 button').prop('disabled',true);
                $('#collapse5 .save_btn').attr('onclick','not_save()');
                }
                 
                
            }
    });
}
function like_training(bundle,option){
    if(option==1){$('.tell_more'+bundle).show();}
    else{$('.tell_more'+bundle).hide();}
}
function enable_others(bundle,option){
    if(option=='Others'){$('#other_target_audience'+bundle).show();}
    else{$('#other_target_audience'+bundle).hide();}
}
/*
function other_WB(bundle,option){
    if(option=='Other') {
        $('#other_wb_cover'+bundle).show();
        $('#other_wb'+bundle).val('1');
        $('#other_work_bundle'+bundle).attr('name','work_bundle[]');
        $('#work_bundle'+bundle).attr('name','');
        
    } else{
        $('#other_wb_cover'+bundle).hide();
        $('#other_wb'+bundle).val('0');
        $('#other_work_bundle'+bundle).attr('name','');
        $('#work_bundle'+bundle).attr('name','work_bundle[]');
    }
}*/


function alternative_participants1(option,id){
    if(option=='Populations as a whole'){
		$('#'+id+'').show();
    }else{
        $('#'+id+'').hide();
    } 
}

function types_of_recurring(){
    var intervention_type = $('input[name=intervention_type]:checked').val(); // retrieve the value
    if(intervention_type=='Yes'){
        $('#intervention_type_cover').show();
    }else{
        $('#intervention_type_cover').hide();
    }
}

function frequency_other1(option){    
    if(option=='Other') {
        $('#frequency_of_session_cover').show();
        
    } else{
        $('#frequency_of_session_cover').hide();
    }
}

function cycle_type(){   // check if the radio is checked
    var cycles_type = $('input[name=cycles_type]:checked').val(); // retrieve the value
    if(cycles_type=='Yes'){
        $('#cycle_type_cover').show();
    }else{
        $('#cycle_type_cover').hide();
    }
}

function types_of_participants1(option){
    var target_population = document.getElementById('types_of_participants');
    var target_population_values = [];
    for (var i = 0; i < target_population.options.length; i++) {        
      if (target_population.options[i].selected) {
        target_population_values.push(target_population.options[i].value);
      }
    }
    var inArray_result=inArray('Other',target_population_values);
    if(inArray_result==true) {
        $('#types_of_participants_other_cover').show();
        
    } else{
        $('#types_of_participants_other_cover').hide();
    }
}
function alternative_activity_other(option){
    if(option=='Other') {
        $('#alternative_activity_other_cover').show();
        
    } else{
        $('#alternative_activity_other_cover').hide();
    }
}
function alternative_activity_types(option){
    if(option=='Other') {
        $('#alternative_activity_types_other_cover').show();
        
    } else{
        $('#alternative_activity_types_other_cover').hide();
    }
}
function activity_types_other1(option,num){
    if(option=='Other') {
        $('#activity_types_other_cover'+num).show();
        
    } else{
        $('#activity_types_other_cover'+num).hide();
    }
}
function prevention_education_intervention1(option){
    if(option=='Other') {
        $('#prevention_education_intervention_other_cover').show();
        
    } else{
        $('#prevention_education_intervention_other_cover').hide();
    }
}
function target_population1(option){
    var target_population = document.getElementById('target_population');
    var target_population_values = [];
    for (var i = 0; i < target_population.options.length; i++) {        
      if (target_population.options[i].selected) {
        target_population_values.push(target_population.options[i].value);
      }
    }
    var inArray_result=inArray('Other',target_population_values);
    if(inArray_result==true) {
        $('#target_population_other_cover').show();
        
    } else{
        $('#target_population_other_cover').hide();
    }
}
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function enable_enddate(bundle)
{
   var wb_ongoing = $('input[class=wb_ongoing'+bundle+']:checked').val(); // retrieve the value
   if(wb_ongoing=='No'){
       $('#end_date_cover'+bundle).show();
       $('#ongoing_yes_cover'+bundle).hide();
   } else{
       $('#end_date_cover'+bundle).hide() ;  
       $('#ongoing_yes_cover'+bundle).show();  
   }
}

function change_servicetype(option,intervention_id,strategy)
{       
    $('.strategy-box.active input[name="inter_strategy_type"]').val(option);
    
	var addd = $("#addressed_issue").val();
	var appstat = $("#approved_state_priority").val();
  
	
    $.ajax({
        url: "get_wb_names.php",
        type: "POST",
        cache: false,
        data:'&service_type=1'+'&strategy_type='+option,
        success: function(html){
            $("#service_type").html(html);
			$("#add_action").val("");			 
        }
    });   
	strategy_type(option,intervention_id,strategy,addd,appstat);	
	
}


function strategy_type(option,intervention_id,strategy,addd,appstat)
{
 	var url;
	if(option=='Prevention Education')
		url = "interventions.php";
	else if(option=='Alternative Drug-Free Activities')
		url = "drug_free_activities.php";
	else if(option=='Environmental')
		url = "environmental_type.php";
	else if(option=='Information Dissemination')
		url = "information_dissemination.php";
	else if(option=='Problem Identification and Referral')
		url = "problem_identification.php";
	else if(option=='Community-Based Processes')
		url = "community_process.php";
	else
		url = "";
		
	if(url!='')
	{
		$("#loader").show();

			$.ajax({
			url: url,
			type: "POST",
			data: '&strategy_type='+option+'&intervention_id='+intervention_id+'&pb=y&addr='+addd+'&appstat='+appstat+'&agency_id='+<?php echo safe_b64decode($_REQUEST['agency']); ?>,
			success: function(html){
			 $('#intervention_parts').html(html);			 
			 $('.disabled_input input,.disabled_input select,.disabled_input textarea,.disabled_input button').prop('disabled',true);
			 $("#loader").hide();			 
			}
		});  
	}
			 
}

//---------Environmental Stratergy type------

function hide_show(name,id){
    var name = $('input[name='+name+']:checked').val(); // retrieve the value
   if(name=='Yes'){
       $('#'+id+'').show();
   } else{
       $('#'+id+'').hide(); 
   }
}
function toogle_select(option,id){
    if(option=='Other'){
       $('#'+id+'').show();
   } else{
       $('#'+id+'').hide(); 
   }
}
function toggle_multiselect(option,id,cover){
    var target_population = document.getElementById(id);
    var target_population_values = [];
    for (var i = 0; i < target_population.options.length; i++) {        
      if (target_population.options[i].selected) {
        target_population_values.push(target_population.options[i].value);
      }
    }
    var inArray_result=inArray('Other',target_population_values);
    if(inArray_result==true) {
        $('#'+cover).show();
        
    } else{
        $('#'+cover).hide();
    }
}
function info_toggle(toggle){
    $('.'+toggle).toggle("slow");
  }
</script>

  </body>
</html>
