<?php

namespace Givemeurl\Linker;

use Givemeurl\Utils\Table;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Обработчик коротких урлов.
 * Редиректит на соответствующий полный урл
 *
 * @author Владислав Васинкин <vlad.vasinkin@yandex.ru>
 */
class Linker
{
    /**
     * @var Table
     */
    private $_table;

    public function __construct(Table $table)
    {
        $this->_table = $table;
    }

    /**
     * @param ServerRequestInterface $request
     * @return EmptyResponse|RedirectResponse
     */
    public function run(ServerRequestInterface $request)
    {
        $short = $request->getUri()->getPath();

        $fullUrl = $this->_table->getFull(str_replace("/", "", $short));

        if (empty($fullUrl)) {
            return new EmptyResponse(404);
        }

        $parsedUrl = parse_url($fullUrl);
        if (empty($parsedUrl["scheme"])) {
            $fullUrl = "http://" . $fullUrl;
        }
        return new RedirectResponse($fullUrl);
    }
}
