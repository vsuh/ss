<?php
header ("Content-Type: text/html; charset=utf-8");
require_once 'vendor/autoload.php';
use Tale\Jade;

var_dump($_GET);
//print_r($_GLOBALS);
var_dump($_POST);
$mode = ($_GET['mode']) ?  $_GET['mode'] : "birthday";

$tpl = 'index';
//Include the vendor/autoload.php if you're using composer!
//include('vendor/autoload.php');

$renderer = new Jade\Renderer([
	        'lifeTime' => 3600,
            'paths' => [__DIR__.'/pub/tpls']

 ]);

echo $renderer->render($tpl,
	[
	'mode' => $mode,
	'title' => 'Дни рождения сотрудников',
	'content' => 'С днем рождения!'
	]);

?>
