<?php

require_once("ApiFrame.class.php"); //Import ApiFrame script

$frame = new Frame();//Init a new instance
$frame->Start();//Frame starten
$frame->enableErrorReporting(false);//Globale errors abfangen, Parameter nur true wenn auch errors abgefangen werden sollen, ohne nur FatalErrors und exceptions







/**
This commtent defines a test API method
@get api/{id:"/^[0-9]*$/"}/{name}
@param text method="get"

*/
 function testAPI($id,$name,$text,$frameObject)
 {
     
     $frame = Frame::get($frameObject);
     
         
     $arr = array("id"=>$id,"name"=>$name,"text"=>$text);
      $frame->output($arr);
    $frame->setHttpStatusCode(200);
  
 }
 
 
$frame->handleApiUrls(__FILE__,$_GET["__url"]);//Behandle API urls benutze für die URL zuordnung den angegebenen GET Parameter [htaccess redirect]
?>