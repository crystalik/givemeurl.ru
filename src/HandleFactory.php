<?php

namespace Givemeurl;

use Givemeurl\Reducer\Reducer;
use Givemeurl\Linker\Linker;

/**
 * Фабрика обработчиков для запросов
 *
 * @author Владислав Васинкин <vlad.vasinkin@yandex.ru>
 */
class HandleFactory
{
    /**
     * @var \PDO
     */
    private $_db;

    public function __construct(\PDO $db)
    {
        $this->_db = $db;
    }

    /**
     * Возвращает объект обработчика с внедрёнными зависимостями
     *
     * @param $name
     * @return Linker|Reducer
     */
    public function get($name)
    {
        switch ($name) {
            case Reducer::class :
                return new Reducer(new Utils\Table($this->_db));
            case Linker::class :
                return new Linker(new Utils\Table($this->_db));

            default:
                throw new \RuntimeException("Не найден обработчик {$name}");
        }
    }
}
