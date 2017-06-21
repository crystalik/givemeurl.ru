<?php

namespace Givemeurl\Reducer;

/**
 * Ответ сокращателя ссылок
 *
 * @author Владислав Васинкин <vlad.vasinkin@yandex.ru>
 */
class Result implements \JsonSerializable
{
    private $_result;

    public function __construct($result)
    {
        $this->_result = $result;
    }

    /**
     * @return array
     */
    function jsonSerialize () {
        return ["result" => $this->_result];
    }
}
