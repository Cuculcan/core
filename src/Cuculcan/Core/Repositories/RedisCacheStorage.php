<?php

namespace Cuculcan\Core\Repositories;

use Predis\Client;

class RedisCacheStorage
{
    protected $client;
    protected $type;
    protected $keyPrefix;
    protected $ttl;

    public function __construct(Client $client, $type, $keyPrefix, $ttl = 86400)
    {
        $this->client = $client;
        $this->type = $type;
        $this->keyPrefix = $keyPrefix;
        $this->ttl = $ttl;
    }

    /**
     * Запись в кэшь
     * @param int $keyId    идентификатор группы записей в кэше 
     * @param int $valueId  идентификатор записи 
     * @param object|array $value   данные
     */
    public function setValue($keyId, $valueId, $value)
    {
        $key = $this->cacheKey($keyId);
        $this->client->hset($key, $valueId, json_encode($value));
        $this->client->expire($key, $this->ttl);
    }

    /**
     * получение данных из кэша
     * @param int $keyId    идентификатор группы записей в кэше 
     * @param int $valueId  идентификатор записи 
     * @return array 
     */
    public function getValue($keyId, $valueId)
    {
        $key = $this->cacheKey($keyId);
        $valuesJson = $this->client->hget($key, $valueId);
        if(!$valuesJson) {
            return null;
        }

        return json_decode($valuesJson, true);
    }

    /**
     * удаление записи   
     * @param int $keyId    идентификатор группы записей в кэше 
     * @param int $valueId  идентификатор записи 
     */
    public function deleteValue($keyId, $valueId)
    {
        $key = $this->cacheKey($keyId);
        $this->client->hdel($key, [$valueId]);
    }

    /**
     * запись значений в группу записей
     * @param int $keyId    идентификатор группы записей в кэше
     * @param array $values массив значений
     */
    public function setAllValues($keyId, array $values)
    {
        if(count($values)>0) {

            $key = $this->cacheKey($keyId);
            $hashValues = [];

            foreach($values AS $id=>$value) {
                $hashValues[$id] = json_encode($value);
            }

            $this->client->hmset($key, $hashValues);
            $this->client->expire($key, $this->ttl);
        }
    }

    /**
     * получение всех значений группы
     * @param int $keyId    идентификатор группы записей в кэше 
     * @return array
     */
    public function getAllValues($keyId)
    {
        $key = $this->cacheKey($keyId);
        $valuesJson = $this->client->hgetall($key);
        if(!$valuesJson) {
            return null;
        }

        $values = [];
        foreach($valuesJson AS $valueJson) {
            $values[] = json_decode($valueJson, true);
        }

        return $values;
    }

    /**
     * Удаляет все записи в группе
     * @param int $keyId    идентификатор группы записей в кэше 
     */
    public function deleteAllValues($keyId)
    {
        $key = $this->cacheKey($keyId);
        $this->client->del([$key]);
    }

    private function cacheKey($keyId)
    {
        return $this->type . $this->keyPrefix . $keyId;
    }
}
