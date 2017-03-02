<?php ob_start(); session_start(); include_once('config.php');
$user_id=$_GET['id'];
$sql_agency = "select id,name FROM agency where id not in (select agency_id FROM agency_map WHERE user_id=".$user_id.")";
$result_agency = mysql_query($sql_agency);
?>
<link rel="stylesheet" type="text/css" href="multiselect/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="multiselect/style.css" />
<link rel="stylesheet" type="text/css" href="multiselect/jquery-ui.css" />
<script type="text/javascript" src="multiselect/1.js"></script>
<script type="text/javascript" src="multiselect/2.js"></script>
<script type="text/javascript" src="multiselect/jquery.multiselect.js"></script>

<select id="agency_id" class="select2-drop form-control" data-init-plugin="select2" multiple name="agency_id[]" >
    <?php while($row_agency = mysql_fetch_array($result_agency)) { ?>
        <option selected value="<?php echo $row_agency['id']; ?>"  ><?php echo $row_agency['name']; ?></option>
    <?php } ?>
</select>
<script type="text/javascript"> $("#agency_id").multiselect(); </script>
