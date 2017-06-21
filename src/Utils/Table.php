<?php

namespace Givemeurl\Utils;

/**
 * Table Gateway для short_url
 *
 * @author Владислав Васинкин <vlad.vasinkin@yandex.ru>
 */
class Table
{
    const NAME = "short_url";
    /**
     * @var \PDO
     */
    private $_db;

    public function __construct(\PDO $db)
    {
        $this->_db = $db;
    }

    /**
     * Получить уже сокращённую ссылку для урл, если есть
     * Сверка идёт по hash
     *
     * @param $hash
     * @return mixed
     */
    public function getExists($hash)
    {
        $query = $this->_db->prepare(
            "SELECT
              short_url
             FROM " .
              self::NAME .
             " WHERE
              hash = ?"
        );
        $query->execute([$hash]);
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Сохранить сокращённую ссылку
     *
     * @param $fullUrl
     * @param $hash
     * @param $shortUrl
     */
    public function saveUrl($fullUrl, $hash, $shortUrl) {
        $this->_db->prepare(
            "INSERT INTO " .
              self::NAME .
             " (hash, url, short_url)
             VALUES
             (?, ?, ?)
             "
        )->execute([$hash, $fullUrl, $shortUrl]);
    }

    /**
     * Получить полный урл
     *
     * @param $short
     * @return string
     */
    public function getFull($short) {
        $query = $this->_db->prepare(
            "SELECT
                url
             FROM " .
                self::NAME .
             " WHERE short_url = ?"
        );
        $query->execute([$short]);
        return $query->fetchColumn();
    }
}
