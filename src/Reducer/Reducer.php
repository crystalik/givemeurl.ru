<?php

namespace Givemeurl\Reducer;

use Givemeurl\Utils\Table;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Сокращатель ссылок
 *
 * @author Владислав Васинкин <vlad.vasinkin@yandex.ru>
 */
class Reducer
{
    const LENGTH = 7;

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
     * @return Result
     */
    public function run(ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $requestedQuery = $request->getUri()->getQuery();
        if (!preg_match("/^url=(?<url>.*)/", $requestedQuery, $matched)) {
            return new Result("");
        }
        $url = trim(urldecode($matched["url"]));
        if (!$this->validateUrl($url)) {
            return new Result("");
        }

        $hash = sha1($url);
        $exists = $this->_table->getExists($hash);
        if ($exists) {
            return new Result($exists["short_url"]);
        }
        $success = false;
        $watchdog = 3; // три попытки сгенерировать уникальную короткую ссылку
        while ($success === false) {
            $shortUrl = $this->_reduce($hash);
            try {
                $this->_table->saveUrl($queryParams["url"], $hash, $shortUrl);
                $success = true;
            } catch (\PDOException $ex) {
                // ловим исключение при дублировании уникального ключа (сгенерировали существующую короткую ссылку)
                if ($watchdog && preg_match("/Duplicate entry '\w+' for key 'short_url'/", $ex->getMessage())) {
                    $watchdog--;
                } else {
                    throw new Exception("Error while save link");
                }
            }
        }

        return new Result($shortUrl);
    }

    /**
     * Простенькая проверка, отсечёт пустые, пробельные, и явно неверные
     * Проверка условная, чтобы совсем уж ненормальных отсечь
     *
     * @param $url
     * @return int
     */
    public function validateUrl($url)
    {
        $parsed = parse_url($url);
        if (!empty($parsed["host"])) {
            return true;
        }
        return preg_match("/(?:\S+)\.(\S+)/i", $parsed["path"]);
    }

    /**
     * Метод генерирует код для ссылки
     *
     * @param string $hash
     * @return string
     */
    private function _reduce($hash)
    {
        $shortUrl = "";
        $casedIndex = rand(2, self::LENGTH-1);
        for ($i = self::LENGTH; $i > 0; $i--) {
            $char = $hash[rand(0, strlen($hash))];
            if ($i % $casedIndex == 0) {
                $char = strtoupper($char);
            }
            $shortUrl .= $char;
        }
        return $shortUrl;
    }
}
