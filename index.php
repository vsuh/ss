<?php
header ("Content-Type: text/html; charset=utf-8");
require_once 'vendor/autoload.php';
use Tale\Jade;

$tpl = 'pub/tpls/index.jade';
//Include the vendor/autoload.php if you're using composer!
//include('vendor/autoload.php');

$renderer = new Jade\Renderer();

echo $renderer->render($tpl);

?>
