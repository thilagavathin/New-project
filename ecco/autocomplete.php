<?php
    include_once('config.php'); 
    $sql="SELECT emailid FROM help";
	$result = mysql_query($sql) or die(mysql_error());
	$dname_list = array();
	if($result)
	{
		while($row=mysql_fetch_array($result))
		{
			$dname_list[] =  $row['emailid'];
		}
        echo json_encode($dname_list);
	}

?>