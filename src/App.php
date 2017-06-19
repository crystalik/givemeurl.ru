<?php

namespace Givemeurl;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequest;

/**
 * Приложение
 *
 * @author Владислав Васинкин <vlad.vasinkin@yandex.ru>
 */
class App
{
    /**
     * @var SapiEmitter
     */
    private $_emitter;
    /**
     * @var Dispatcher
     */
    private $_dispatcher;

    public function __construct(SapiEmitter $emitter, Dispatcher $dispatcher)
    {
        $this->_emitter = $emitter;
        $this->_dispatcher = $dispatcher;
    }

    public function run(ServerRequest $request)
    {
        $response = $this->_invoke($request, new Response());
        $this->emitter->emit($response);
    }

    protected function _invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $route = $this->_dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                throw new \RuntimeException("Ресурс не найден", 404);

            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new \RuntimeException("Недопустимый запрос");

            case \FastRoute\Dispatcher::FOUND:
                var_dump($route);
                die;
        }
    }
}
