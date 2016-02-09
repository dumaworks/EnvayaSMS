<?php

if (sizeof($argv) == 4){
  $sender = $argv[1];
  $message = $argv[3];
  $subject = $argv[2];
  $headers = "From: webmaster@dumaworks.net" . "\r \n" . "Reply-To:" . $sender . "\r \n";
  $to = "dumastaff@gmail.com";
  $result = mail($to,$subject,$message,$headers);
  if ($result == 1) {
    echo "Message Sent Successfully. \r \n We shall get back to you within the next 24 hours. Thank you. ";
  }
  else {
    echo " Error Message has not been sent. \r \n We are having trouble sending your message. Please email us at dumaworks@gmail.com. We apologize for the i\
nconvenience";
  }

}
else{
  error_log("Usage: php send_email.php <youremail> \"<subject>\" \"<message>\"");
  echo (" Error Message has not been sent. \r \n We are having trouble sending your message. Please email us at dumaworks@gmail.com. We apologize for the in\
convenience");
}
?>
