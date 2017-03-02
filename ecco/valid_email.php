<?php
    include_once('config.php');
   //echo $_GET['id'];
    $sql="SELECT count(*) as cnt FROM help where emailid ='".trim($_GET['id'])."'";
	$result_mail = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($result_mail);
    
    while($row=mysql_fetch_array($result_mail))
		{
		    $chkval = $row['cnt'];
		  }
    
    if($chkval==1)
	{
	    echo '1';
     }
     else {
        echo '0';
     }

?>