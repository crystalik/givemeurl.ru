<?php

namespace Givemeurl;

use FastRoute\Dispatcher as DispatcherInterface;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;

/**
 * Class Dispatcher
 * @package Givemeurl
 */
class Dispatcher implements DispatcherInterface
{

    private $_dispatcher;

    public function __construct()
    {
        $routes = new RouteCollector(new RouteParser, new DataGenerator);

        $routes->addRoute("GET", "/getshort[/]", Reducer::class);
        $routes->addRoute("GET", "/{short:\w+}", Linker::class);

        $this->_dispatcher = new GroupCountBasedDispatcher($routes->getData());

    }

    public function dispatch($httpMethod, $uri)
    {
        return $this->_dispatcher->dispatch($httpMethod, $uri);
    }
}
