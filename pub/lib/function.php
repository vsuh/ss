<?php
try {  
    $client = @new SoapClient($_ws);  
	} catch (Exception $e) {  
    echo "<hr><b>ERROR:(".basename(__FILE__).":".__LINE__.") </b>".$e->getMessage()."<hr>";
	$client = null;	
	exit();
} 


  ini_set("soap.wsdl_cache_enabled","0"); 
  $_ws = "http://obr-mdb-01/wsb/ws/wsSotrList.1cws?wsdl";
  $debug_flag = true;

?>