<?php
require_once "vendor/autoload.php";

$emitter = new \Zend\Diactoros\Response\SapiEmitter();
$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$dispatcher = new \Givemeurl\Dispatcher();
$configDB = Symfony\Component\Yaml\Yaml::parse(file_get_contents("config/config.yml"))["Database"];

$dsn = $configDB["driver"] . ":dbname={$configDB["dbname"]};host={$configDB["host"]};port={$configDB["port"]}";
$database = new \PDO($dsn, $configDB["username"], $configDB["password"]);
$database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$handlerFactory = new \Givemeurl\HandleFactory($database);

(new Givemeurl\App($emitter, $dispatcher, $handlerFactory))->run($request);
