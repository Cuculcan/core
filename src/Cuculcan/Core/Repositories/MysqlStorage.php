<?php

namespace Cuculcan\Core\Repositories;

use Cuculcan\Core\Interfaces\StorageInterface;
use Cuculcan\Core\Interfaces\EntityInterface;

class MysqlStorage implements StorageInterface
{

    /**
     * коннект с БД
     * @var \PDO
     */
    protected $connection;

    /**
     * @var string
     */
    private $table;

    public function __construct(\PDO $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function add(EntityInterface $item, $insertIgnore = false)
    {
        $fields = $item->export();

        $fieldNames = [];
        $fieldValues = [];
        $insertData = [];

        foreach($fields AS $field) {
            if($field['name']=='id') {
                continue;
            }
            $fieldNames[] = "`" . $field['name'] . "`";
            $fieldValues[] = ":" . $field['name'];
            $insertData[$field['name']] = $field['value'];
        }

        $sql = 'INSERT'.($insertIgnore ? ' IGNORE ' : ' ').'INTO '.$this->table.' ('.implode(',', $fieldNames) .') VALUES ('.implode(',', $fieldValues).')';
        $stm = $this->connection->prepare($sql);
        $stm->execute($insertData);

        return $this->connection->lastInsertId();
    }

    public function addBatch(array $entities, $updateDuplicate = false, $insertIgnore = false)
    {
        $allData = [];
        foreach($entities AS $entity) {
            $allData[] = $entity->export();
        }

        $dataToInsert = array();
        $colNames = [];
        foreach($allData AS $row) {
            $colNames = [];
            foreach($row AS $field) {
                if($updateDuplicate) {
                    // Если зачение не установлено, то мы не будем использовать это поле для записи
                    if(($field['value']==null || $field['value']=='null') && $field['value']!==0) {
                        continue;
                    }
                }
                else {
                    if($field['name'] == 'id') {
                        continue;
                    }
                }
                $dataToInsert[] = $field['value'];
                $colNames[] = '`'.$field['name'].'`';
            }
        }

        $rowPlaces = '('.implode(', ', array_fill(0, count($colNames), '?')).')';
        $allPlaces = implode(', ', array_fill(0, count($allData), $rowPlaces));

        $sql = 'INSERT'.($insertIgnore ? ' IGNORE ' : ' ').'INTO '.$this->table.' ('.implode(', ', $colNames).') VALUES '.$allPlaces;

        if($updateDuplicate) {
            $updateCols = array();
            foreach($colNames AS $curCol) {
                $updateCols[] = $curCol.' = VALUES('.$curCol.')';
            }
            $onDup = implode(', ', $updateCols);
            $sql = $sql.' ON DUPLICATE KEY UPDATE '.$onDup;
        }

        $stmt = $this->connection->prepare($sql);

        try {
            $stmt->execute($dataToInsert);
        } catch(PDOException $e) {
            //echo $e->getMessage();
            return  0;
        }
        
        return $this->connection->lastInsertId();
    }

    /**
     * Обновление данных модели по id
     *
     * @param EntityInterface $item Модель для обновления (должен быть установлен id)
     * @param array $updateFields Массив имен полей для обновления. Если не установлен обновятся все поля модели
     */
    public function update(EntityInterface $item, array $updateFields = null)
    {
        $fields = $item->export();

        $fieldsSet = [];
        $insertData = [];

        foreach($fields AS $field) {

            if($updateFields) {
                // Если задан список полей для обновления
                if(!in_array($field['name'], $updateFields) && $field['name']!='id') {
                    // Если поле не в списке, пропускаем
                    continue;
                }
            }

            $insertData[$field['name']] = $field['value'];

            if($field['name']=='id') {
                continue;
            }

            $fieldsSet[] = "`" . $field['name'] . "`=:" . $field['name'];
        }

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $fieldsSet) . ' WHERE id=:id';

        $stm = $this->connection->prepare($sql);
        $stm->execute($insertData);
    }

    /**
     * Обновление данных списка моделей по id
     * 
     * @param array $items Список моделей для обновления (должен быть установлен id)
     * @param array $updateFields Kеу-value массив полей для обновления.
     */
    public function updateAllByOne(array $items, EntityInterface $item, array $updateFields)
    {
        if(count($items)==0) {
            return;
        }
        
        $fields = $item->export();

        $fieldsSet = [];
        $insertData = [];

        foreach($fields AS $field) {

            if(!in_array($field['name'], $updateFields)) {
                // Если поле не в списке, пропускаем
                continue;
            }
            
            if($field['name'] == 'id') {
                continue;
            }

            $fieldsSet[] = "`" . $field['name'] . "`=?";
            $insertData[] = $field['value'];
        }

        $inArray = [];
        foreach($items AS $item) {
            $inArray[] = '?';
            $insertData[] = $item->getId();
        }
        
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $fieldsSet) . ' WHERE id IN ('. implode(',', $inArray). ')';

        $stm = $this->connection->prepare($sql);
        $stm->execute($insertData);
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id = ' . $id;
        $stm = $this->connection->prepare($sql);
        $stm->execute();
    }

    public function deleteByIds(array $ids)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id IN (' . implode(',', $ids) . ')';
        $stm = $this->connection->prepare($sql);
        $stm->execute();
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = ' . $id;
        $stm = $this->connection->prepare($sql);
        $stm->execute();
        $res = $stm->fetch(\PDO::FETCH_ASSOC);
        return $res;
    }

    public function getByIds(array $ids)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id IN (' . implode(',', $ids) . ')';
        $stm = $this->connection->prepare($sql);
        $stm->execute();
        $items = [];
        while ($row = $stm->fetch(\PDO::FETCH_ASSOC)) {
            $items[] = $row;
        }
        return $items;
    }

}
