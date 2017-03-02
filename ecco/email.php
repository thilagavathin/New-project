<?php

    $to   = 'muhammedgn@gmail.com';
    $email ='muhammedgani@vividinfotech.com';
    $from = $email;
    
    $subject ="Account activation";
    $headers = "From: " . strip_tags($from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $message = '<html><body>';
    
    $message .= '<table width="100%"; rules="all" style="border:1px solid #3A5896;" cellpadding="10">';
    
    $message .= "<tr><td colspan=2>Hello admin,<br /><br />Thanks for registering at http://ecco.ga-sps.org/</td></tr>";
    
    $message .= "<tr><td colspan=2 font='colr:#999999;'>Here are your account details:</td></tr>"; 
    $message .= "<tr><td colspan=2 font='colr:#999999;'>User Name :".$email."</td></tr>"; 
    $message .= "<tr><td colspan=2 font='colr:#999999;'>Password :*hidden*</td></tr>"; 
    $message .= "<tr><td colspan=2 font='colr:#999999;'>You will first have to activate your account by clicking on the following link:</td></tr>";
     
    
    $message .= "</table>";
    
    $message .= "</body></html>";  
    
    echo '<pre>'; print_r($message);
    mail($to, $subject, $message, $headers);


?>