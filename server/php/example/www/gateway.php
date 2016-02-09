<?php

/* 
 * This example script implements the EnvayaSMS API. 
 *
 * It sends an auto-reply to each incoming message, and sends outgoing SMS
 * that were previously queued by example/send_sms.php .
 *
 * To use this file, set the URL to this file as as the the Server URL in the EnvayaSMS app.
 * The password in the EnvayaSMS app settings must be the same as $PASSWORD in config.php.
 */

require_once dirname(__DIR__)."/config.php";
require_once dirname(dirname(__DIR__))."/EnvayaSMS.php";

//Visits a site and prints whatever it finds.
function visitSite($url, $action){
  // connect via SSL, but don't check cert
  
  $handle=curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($handle, CURLOPT_POST, 2);
  curl_setopt($handle, CURLOPT_POSTFIELDS, "from={$action->from}&message={$action->message}&timestamp={$action->timestamp}");
  $content = curl_exec($handle);
  curl_close($handle);
  
  return $content; // show target page
}



$request = EnvayaSMS::get_request();

header("Content-Type: {$request->get_response_type()}");

if (!$request->is_validated($PASSWORD))
{
    header("HTTP/1.1 403 Forbidden");
    error_log("Invalid password");    
    echo $request->render_error_response("Invalid password");
    return;
}

$action = $request->get_action();

switch ($action->type)
{
     case EnvayaSMS::ACTION_INCOMING:    
       
       error_log("Received {$action->message_type} from {$action->from}: {$action->message}");
       visitSite("https://dumaworks.com/sms/", $action);
       echo $request->render_response();
       return;
       
     case EnvayaSMS::ACTION_OUTGOING:
        $messages = array();
   
        // In this example implementation, outgoing SMS messages are queued 
        // on the local file system by send_sms.php. 
          
        $dir = opendir($OUTGOING_DIR_NAME);
        while ($file = readdir($dir)) 
        {
            if (preg_match('#\.json$#', $file))
            {
                $data = json_decode(file_get_contents("$OUTGOING_DIR_NAME/$file"), true);
                if ($data)
                {
                    $sms = new EnvayaSMS_OutgoingMessage();
                    $sms->id = $data['id'];
                    $sms->to = $data['to'];
                    $sms->message = $data['message'];
                    $messages[] = $sms;
                }
            }
        }
        closedir($dir);
        
        $events = array();
        
        if ($messages)
        {
            $events[] = new EnvayaSMS_Event_Send($messages);
        }
        
        echo $request->render_response($events);

        return;
        
    case EnvayaSMS::ACTION_SEND_STATUS:
    
        $id = $action->id;
        
        error_log("message $id status: {$action->status}");
        
        // delete file with matching id    
        if (preg_match('#^\w+$#', $id))
        {
            unlink("$OUTGOING_DIR_NAME/$id.json");
        }
        echo $request->render_response();        
        
        return;
    case EnvayaSMS::ACTION_DEVICE_STATUS:
        error_log("device_status = {$action->status}");
        echo $request->render_response();
        return;             
    case EnvayaSMS::ACTION_TEST:
        echo $request->render_response();
        return;                             
    default:
        header("HTTP/1.1 404 Not Found");
        echo $request->render_error_response("The server does not support the requested action.");
        return;
}
