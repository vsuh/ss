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
  $ret['000000000'] = 'Не выбрано (по всем)';
  $result = $client->GetDepsList();
  $strDeps = $result->return;

  foreach((array)$strDeps as $obj){
    foreach($obj as $vvv){
      $vvv = (array)$vvv;
      $ret[$vvv['DepCode']] = $vvv['DepName'];
    }
  }
  $ret = walkDeps($ret);

  return $ret;
}


function get_bd_content($depCode){
  global $client;
  if($client === null){
    $client = ws_init();
  }
  	$param["dcode"] = (empty($depCode) || $depCode == '000000000') ? NULL : $depCode;
   try{
		$result = $client->getHBD($param);
	} catch(SoapFault $e){
		echo "<b>".__LINE__."-> ".$e->faultstring."</b><br/><br/>";
	}
  $ret = $result->return;
  $ret = normalizeBirthDays($ret);
  // var_dump($ret);
  return $ret;
}


function walkDeps($depArr){
  $arr = array();
  foreach($depArr as $key => $val ){

    $arr[str_pad($key, 9, "0", STR_PAD_LEFT)] = $val;
  }

  return $arr;

}
function get_list_employee($currDep, $post){
  $ret = array();
  $client = ws_init(); 
  if($client == null) {
        die("Не удалось получить список сотрудников");
  }
	
	$param["dcode"] = ($currDep == "000000000") ? NULL : $currDep;

  try{
		$result = $client->getEmpList($param);
 	}catch(SoapFault $E){
		echo "<b>Err: ".__LINE__."-> ".$E->faultstring."</b><br/><br/>";
	}
  
  return normalizeMovings($result);
}

function get_vacations($currDep, $post){
  $ret = array();
   $client = GetSoapClientObj($_ws); 
  if($client==NULL) {return;}
	$ret = "";
	$param["dcode"] = (empty($_POST) or $_POST['depCode'] == "") ? NULL : $_POST['depCode'];
   try{
		$result = $client->getAbsent($param); 
	}catch(SoapFault $E){
		echo "<b>".__LINE__."-> ".$E->faultstring."</b><br/><br/>";
	}

  return $ret;
}

function get_fired_list($currDep, $post){
  $ret = array();
   if (!$_POST['to'] or !$_POST['from']) return "Err: Неверный период";
  if ($_POST['to'] < $_POST['from']) return "Err: Неверный период";

  $client = GetSoapClientObj($ws); 
  if($client == NULL) {return;}
	$ret = "";
	$param["dcode"] = ($_POST['depCode'] == "0") ? NULL : $_POST['depCode'];
	$param["PeriodBeg"] = strtotime($_POST['from']);
	$param["PeriodEnd"] = strtotime($_POST['to']);
 
  try{
		$result = $client->getFired($param); 
	}catch(SoapFault $E){
		echo "<b>".__LINE__."-> ".$E->faultstring."</b><br/><br/>";
		return $ret;
	}

  return $ret;
}
function get_mv_content($currDep, $post){
  if(empty($post['rep-period-from'])) {
    die('Неверная дата начала периода отчета: '.$post['rep-period-from'].'<a href="/?mode=mv">Назад</a>');
  }
  $from = strtotime($post['rep-period-from']);
  if(empty($post['rep-period-to'])){
    die('Неверная дата завершения периода отчета: '.$post['rep-period-to'].'<a href="/?mode=mv">Назад</a>');
  }
  $to = strtotime($post['rep-period-to']);
  if ($to < $from) {
    die("Дата начала периода отчета ".$post['rep-period-from']." больше даты завершения ".$post['rep-period-to'].'<a href="/?mode=mv">Назад</a>');
  }
  if(0+$post["rep-purp"]< 0 || 0+$post["rep-purp"]> 3){
    die("Указана неверная причина перемещения");
  }
  $client = ws_init(); 
  if($client == null) {
    die("Не удалось получить данные по перемещениям");
    }
	
	$param["dcode"] = ($currDep == "000000000") ? NULL : $currDep;
	$param["PeriodBeg"] = $from;
	$param["PeriodEnd"] = $to;
	$param["EventType"] = $post["rep-purp"];

   try{
		$result = $client->getMoved($param); 
    // var_dump($result);
	}catch(SoapFault $E){
		echo "<b>".__LINE__."-> ".$E->faultstring."</b><br/><br/>";
	}
  return normalizeMovings($result);
  }

function normalizeMovings($mv){
  $ret = array();
  $mv = (array)$mv;
  $mv = (array)$mv['return'];
  // var_dump(gettype($mv['arrStr']));
  if(gettype($mv['arrSTR']) == 'object'){
    $mv = (array)$mv['arrSTR'];
    array_push ($ret, $mv);
  } else {
   $mv = (array)$mv['arrSTR'];
    foreach($mv as $val)
    array_push($ret, (array)$val);
  }
  return $ret;
}


function normalizeBirthDays($bd){
  $ret = array();
  $mon = get_month_array();
  $bd = (array)$bd;
  if(gettype($bd['arrSTR']) == 'object'){
    $bd = (array)$bd['arrSTR'];
    array_push ($ret, $bd);
  } else {
   $bd = (array)$bd['arrSTR'];
    foreach($bd as $val)
    array_push($ret, (array)$val);
  }
  foreach($ret as $k=>$val){
    $ret[$k]['HBDay'] = $val['HBDay']." ".$mon[$val['HBMonth']];
    if($val['HBAge']%5 == 0) {
      $ret[$k]['HBMonth'] = true;
    } else {
      $ret[$k]['HBMonth'] = false;
    }
  }
return $ret;
}

function get_month_array(){
  return [
    1 => "Янв",
    2 => "Фев",
    3 => "Мар",
    4 => "Апр",
    5 => "Май",
    6 => "Июн",
    7 => "Июл",
    8 => "Авг",
    9 => "Сен",
    10 => "Окт",
    11 => "Ноя",
    12 => "Дек",
  ];
}

function getTemplate($cmd){
  $tpl = '';
  switch ($cmd){
    case 'bd' : $tpl = 'index' ; break;
    case 'mv' : $tpl = 'move'  ; break;
    case 'ls' : $tpl = 'list' ; break;
    case 'vc' : $tpl = 'vacation' ; break;
    case 'fr' : $tpl = 'fired' ; break;
    }
    $tpl = __DIR__ . "\\..\\tpls\\" . $tpl . ".jade" ;
    return $tpl;
}
?>