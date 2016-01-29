<?php

 function ws_init() {
	  ini_set("soap.wsdl_cache_enabled","0");
	  $_ws = 'http://'.$_SERVER["HTTP_HOST"]."/wsb/ws/wsSotrList.1cws?wsdl";
	  $dbg = true;

	try {
	    $client = @new SoapClient($_ws);
		} catch (Exception $e) {
	    echo "<hr><b>ERROR:(".basename(__FILE__).":".$e.line.") </b>".$e->getMessage()."<hr>";
		$client = null;
		exit();
	}

  return $client;
}
function getDepartmentList() {
  global $client;
  if($client === null){
    $client = ws_init();
  }
  $ret = array();
  $result = $client->GetDepsList();
  $strDeps = $result->return;

  foreach((array)$strDeps as $obj){
    foreach($obj as $vvv){
      $vvv = (array)$vvv;
      $ret[$vvv['DepCode']] = $vvv['DepName'];
    }
  }
  return $ret;
}


function get_bd_content($depCode){
  global $client;
  if($client === null){
    $client = ws_init();
  }
  	$param["dcode"] = (empty($depCode)) ? NULL : $depCode;
   try{
		$result = $client->getHBD($param);
	} catch(SoapFault $e){
		echo "<b>".__LINE__."-> ".$e->faultstring."</b><br/><br/>";
	}
  $ret = (array)$result->return;
  $ret = $ret['arrSTR'];
  return $ret;
}

?>