<?php
  echo "-------------yes, i'm lamo-----<br><br>";
  require_once 'vendor/autoload.php';
  require_once 'pub/lib/function.php';

  use Jade\Jade;

  $jade 		= new Jade();
  $tpl_dir 	= __DIR__.'\pub\tpls';
  $jext 		= '.jade';
  $client   = null;
  //var_dump($_POST);
  $depList = getDepartmentList();

  $mode = ($_GET['mode']) ?  $_GET['mode'] : 'bd';
  $currDep = ($_POST['rep_dept']) ? $depList[$_POST['rep_dept']] : null;
  $tpl = $tpl_dir . '\index' . $jext;
  $bd = get_bd_content($_POST['rep_dept']);


var_dump($bd);
  $jade->render($tpl,
    [
      'currDep' => $currDep,
      'depList' => $depList,
      'mode' => $mode,
      'title' => 'Списки сотрудников сотрудников',
      'content' => $bd
    ]);


?>
