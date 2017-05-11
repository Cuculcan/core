<?php

namespace Cuculcan\Core\Model;

use Cuculcan\Core\Model\Interfaces\StorageInterface;
use Cuculcan\Core\Model\Interfaces\EntityInterface;

class MysqlStorage implements StorageInterface
{

    private $db;
    private $table;

    public function __construct($connection, $table)
    {
        $this->db = $connection;
        $this->table = $table; 
    }

    public function add(EntityInterface $item)
    {
        $fields = $item->export();

        $fieldNames = [];
        $fieldValues = [];
        $insertData = [];
        foreach ($fields as $fieldName => $value) {
            if ($fieldName == "id") {
                continue;
            }

            $fieldNames[] = "`" . $fieldName . "`";
            $fieldValues[] = ":" . $fieldName;
            $insertData[$fieldName] = $value;
        }

        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(",", $fieldNames) . ') VALUES (' . implode(',', $fieldValues) . ')';
        $stm = $this->db->prepare($sql);
        $stm->execute($insertData);

        return $this->db->lastInsertId();
    }

    public function update(EntityInterface $item)
    {
        $fields = $item->export();

        $fieldsSet = [];
        $insertData = [];
        foreach ($fields as $fieldName => $value) {
            if ($fieldName == "id") {
                continue;
            }

            $fieldsSet[] = "`" . $fieldName . "`=:".$fieldName;
        }

        $sql = 'UPDATE  ' . $this->table . ' SET ' . implode(",", $fieldsSet) . ' WHERE id=:id ';
        $stm = $this->db->prepare($sql);
        $stm->execute($fields);
        
    }

    public function delete(EntityInterface $item)
    {
        
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id='.$id ;
        $stm = $this->db->prepare($sql);
        $stm->execute();
        $res = $stm->fetch(\PDO::FETCH_ASSOC);
        return $res;
    }

    public function getByIds(array $ids)
    {
        
    }

}
