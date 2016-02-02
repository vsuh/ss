<?php

require_once 'vendor/autoload.php';
require_once 'pub/lib/function.php';

use Jade\Jade;

$jade = new Jade();
$tpl_dir = __DIR__.'\pub\tpls';
$jext = '.jade';


echo "<br><br><br><br>";
//  var_dump($_POST);
$depList = getDepartmentList();

$mode = ($_GET['mode']) ? $_GET['mode'] : 'bd';
$currDep = ($_POST['rep_dept']) ? $_POST['rep_dept'] : "000000000";
$tpl = getTemplate($mode);

if ($_POST['go']) {
  switch ($mode) {
    case 'bd':
      $content = get_bd_content($currDep);
      break;
    case 'mv':
      $content = get_mv_content($currDep, $_POST);
      break;
    case 'ls':
      $content = get_list_employee($currDep);
      break;
    case 'vc':
      $content = get_vacations($currDep);
      break;
    case 'fr':
      $content = get_fired_list($currDep, $_POST);
      break;
  }
}

//  echo 'content';
  //  var_dump($content);
//  var_dump($tpl);
$jade->render($tpl, [
  'currDepCode' => $currDep,
  'currDep' => ($currDep == "000000000") ? "" : $depList[$currDep],
  'depList' => $depList,
  'mode' => $mode,
  'title' => 'Списки сотрудников сотрудников',
  'content' => (is_array($content)) ? $content : array(),
  'arrMonth' => get_month_array(),
  'PeriodBeg' => ($_POST['rep-period-from']) ? $_POST['rep-period-from'] :date('01-m-Y'),
  'PeriodEnd' => ($_POST['rep-period-to']) ? $_POST['rep-period-to'] : date('t-m-Y'),
  'go' => $_POST['go'],
  'reason' => $_POST['rep-purp']
  ]);

?>