<?php
include_once('config.php');
include_once('strategy_fn.php');

$c = (isset($_POST['intervention_id']))?$_POST['intervention_id']:''; 
$name = (isset($_POST['name']))?$_POST['name']:'';
$strategy_type = (isset($_POST['stype']))?$_POST['stype']:'';

 $c1 = $c-1;
$html = '
<div class="row portfolio_workbundle pad_t20">
<div class="form-group col-md-12">
<label class="add_button ele_bl text_white pad10 mar0 "><span class="bold">1. </span>'.$name.'</label></div>
<div class="col-md-6 col-xs-12">
<div class="form"><div class="form-group"><label><span class="text_blue bold">2. </span>Work Bundle Name</label>
<select class="form-control" name="work_bundle[]"><option value="">Select</option>';
$strategy = get_strategy_type($strategy_type);
$strategy_items = get_Wb_items($strategy);
while($items=mysql_fetch_array($strategy_items))
{  
	 $html.=' <option value="'.$items[1].'">'.$items[1].'</option>';
}  
  $html.='
</select>
<input type="text" class="form-control" name="other_work_bundle[]" id="other_work_bundle'.$c1.'" style="display:none;" />
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
                          <select class="form-control" name="target_audience[]" onchange="enable_others('.$c1.',this.value)">
                          <option value="">Select</option>
                          <option>Project Population</option>
                          <option>School Admin</option>
                          <option >Local Board Members</option>
                          <option>State Board Members</option>
                          <option>Community Key Stakeholdres</option>
                          <option >Partnering Agencies</option>
                          <option>Law Enforcement</option>
                          <option >Public Health</option>
                          <option >Others</option>
                          </select>
                        </div>
<div class="form-group"><input id="other_target_audience'.$c1.'" style="display: none;"  type="text" class="form-control"  name="other_target_audience[]" placeholder="Others Target Audience" /></div>
<div class="form-group"><label class="ele_bl"><span class="text_blue bold">6. </span>Is this Work Bundle ongoing?</label>
<label class="checkbox_normal mar_r15"><input class="wb_ongoing'.$c1.'" onclick="enable_enddate('.$c1.')" type="radio" name="end_status[]" value="Yes"><span class="custom-icon radio-icon"></span>Yes</label>
<label class="checkbox_normal"><input class="wb_ongoing'.$c1.'" onclick="enable_enddate('.$c1.')" type="radio" name="end_status[]" value="No"><span class="custom-icon radio-icon"></span>No</label></div>
<div class="form-group" style="display: none;" id="ongoing_yes_cover'.$c1.'"><label><span class="text_blue bold">6.a </span>Brief explanation</label><input type="text" class="form-control" name="ongoing_explain[]"></div><div class="row" style="display: none;" id="end_date_cover'.$c1.'" ><div class="col-md-6 form-group"><label><span class="text_blue bold">6.b </span>Projected Start Date</label>
<div class="input-group date form_date" data-date-format="mm/dd/yyyy"><input type="text" readonly="" class="form-control" name="start_date[]" ><span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span></div></div>
<div class="form-group col-md-6"><label><span class="text_blue bold"></span>Projected End Date</label><div class="input-group date form_date" data-date-format="mm/dd/yyyy"><input type="text" readonly="" class="form-control" name="end_date[]"><span class="input-group-addon no-border dark_bluebg text_white"><span class="glyphicon glyphicon-calendar"></span></span></div></div></div><div class="form-group"><label class="ele_bl"><span class="text_blue bold">7. </span>Would you like Training or TA on this Work Bundle?</label><label class="checkbox_normal mar_r15"><input onclick="like_training('.$c1.',1)" type="radio" name="like_training['.$c1.']" value="Yes" ><span class="custom-icon radio-icon"></span>Yes</label>
<label class="checkbox_normal"><input onclick="like_training('.$c1.',0)" type="radio" name="like_training['.$c1.']" value="No" ><span class="custom-icon radio-icon"></span>No</label></div><div class="form-group tell_more'.$c1.'" style="display: none;">
<label>Tell us more</label><textarea class="form-control mar_b20" name="about_training[]"></textarea></div></div></div>
<div class="col-md-12 col-xs-12"><div class="form"><div class="form-group"><label><span class="text_blue bold">8. </span>Action Steps</label>
<div id="action_steps'.$c1.'" class="action_steps form col-md-12"><div class="col-md-6 col-xs-12">
<div class="form-group"><input type="text" class="form-control" placeholder="Action Step" name="activities['.$c1.'][]"  ></div></div>
<div class="col-md-6 col-xs-12"><div class="form-group"><input type="text" class="form-control" name="activities['.$c1.'][]" ></div></div></div></div></div></div>
<div class="col-md-12 form"><input type="hidden" id="add_action_count'.$c1.'" value="1" /><button type="button" class="add_button mar_b15" onclick="add_action_steps('.$c1.')"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add Action Steps</button></div></div>';

echo $html;
?>