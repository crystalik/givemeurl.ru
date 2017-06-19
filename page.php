<?php
require_once "vendor/autoload.php";

$emitter = new \Zend\Diactoros\Response\SapiEmitter();
$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$dispatcher = new \Givemeurl\Dispatcher();

(new Givemeurl\App($emitter, $dispatcher))->run($request);
