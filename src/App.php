<?php

namespace Givemeurl;

use Givemeurl\Reducer\Exception as ReducerException;
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
    /**
     * @var HandleFactory
     */
    private $_factory;

    public function __construct(SapiEmitter $emitter, Dispatcher $dispatcher, HandleFactory $factory)
    {
        $this->_emitter = $emitter;
        $this->_dispatcher = $dispatcher;
        $this->_factory = $factory;
    }

    public function run(ServerRequest $request)
    {
        $response = $this->_invoke($request, new Response());
        $this->_emitter->emit($response);
    }

    /**
     * Опработчик запроса
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return array|mixed|ResponseInterface|Response\EmptyResponse|Response\RedirectResponse|static
     */
    protected function _invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $route = $this->_dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                throw new \RuntimeException("Ресурс не найден", 404);

            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new \RuntimeException("Недопустимый запрос", 405);

            case \FastRoute\Dispatcher::FOUND:
                if (!is_callable([$route[1], "run"])) {
                    throw new \RuntimeException("Ошибка обработчика", 500);
                }
                try {
                    $result = call_user_func_array([$this->_factory->get($route[1]), "run"], [$request]);
                } catch (ReducerException $ex) {
                    $result = ["error" => $ex->getMessage()];
                }

                if ($result instanceof Response\RedirectResponse || $result instanceof Response\EmptyResponse) {
                    $response = $result;
                } else {
                    $response = $response->withHeader("Content-type", "application/json; utf-8");

                    $response->getBody()->write(json_encode($result));
                }
                break;
        }

        return $response;
    }
}
