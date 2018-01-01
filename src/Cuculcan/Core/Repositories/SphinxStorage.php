<?php

namespace Cuculcan\Core\Repositories;

use Cuculcan\Core\Repositories\SearchParams;
use Cuculcan\Core\Interfaces\EntityInterface;

class SphinxStorage
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

    public function insertOrUpdate(EntityInterface $item)
    {
        $fields = $item->exportSphinx();

        $fieldNames = [];
        $fieldValues = [];
        $insertData = [];

        foreach($fields as $field) {
            $fieldNames[] = "`" . $field['name'] . "`";
            if($field['type']=='array') {
                $fieldValues[] = $field['value'];
            }
            else {
                $fieldValues[] = ":" . $field['name'];
                $insertData[$field['name']] = $field['value'];
            }
        }

        $sql = 'REPLACE INTO ' . $this->table . ' (' . implode(',', $fieldNames) . ') VALUES (' . implode(',', $fieldValues) . ')';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($insertData);
    }

    public function insertOrUpdateBatch(array $items)
    {
        $allData = [];
        foreach($items as $item) {
            $allData[] = $item->exportSphinx();
        }
        
        $dataToInsert = array();
        $colNames = [];
        $rows = [];

        foreach($allData AS $row=>$data) {

            $colNames = [];
            $rowPlaces = [];

            foreach($data as $value) {
                $colNames[] = '`'.$value['name'].'`';
                if($value['type']=='array') {
                    $rowPlaces[] = $value['value'];
                } else {
                    $rowPlaces[] = '?';
                    $dataToInsert[] = $value['value'];
                }                
            }
            
            $rows[] = '('.implode(', ', $rowPlaces). ')';
        }

        $sql = 'REPLACE INTO ' . $this->table . ' (' . implode(', ', $colNames) . ') VALUES ' . implode(', ', $rows);
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($dataToInsert);
    }

    /**
     * Удаляет записи из сфинкса по имени колонки и значению
     * @param type $field  имя поля
     * @param type $value  значение
     */
    public function deleteByFieldValue($field, $value)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE " . $field . " = " . $value;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
    }
    
    /**
     * Удаляет записи из сфинкса по имени колонки и значениям
     * @param type $field имя колонки
     * @param array $values массив значений
     */
    public function deleteByFieldValues($field, array $values, $type = 'int')
    {

        if(count($values)>0) {

            if($type !== 'int') {
                $inQuery = implode(',', array_fill(0, count($values), '?'));
            }
            else {
                $inQuery = implode(',', $values);
            }

            $sql = "DELETE FROM " . $this->table . " WHERE " . $field . " IN (" . $inQuery . ")";
            $stmt = $this->connection->prepare($sql);

            if($type !== 'int') {
                $stmt->execute($values);
            }
            else {
                $stmt->execute();
            }
        }
    }

    public function getColumnsBySearchParams($columns, SearchParams $searchParams)
    {
        $searchRequest = $searchParams->build();

        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $this->table . ' ' . $searchRequest['sql'];

        $stmt = $this->connection->prepare($sql);

        foreach($searchRequest['values'] AS $key=>$value) {
            if(gettype($value)=='integer') {
                $stmt->bindValue(($key+1), $value, \PDO::PARAM_INT);
            }
            else {
                $stmt->bindValue(($key+1), $value, \PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        $items = [];

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $items[] = $row;
        }

        return $items;
    }

    public function getRowsCountBySearchParams(SearchParams $searchParams)
    {
        $searchRequest = $searchParams->build();
        $sql = 'SELECT COUNT(*) AS rows_amount FROM ' . $this->table . ' ' . $searchRequest['sql'];
        $stmt = $this->connection->prepare($sql);
        foreach($searchRequest['values'] AS $key=>$value) {
            if(gettype($value)=='integer') {
                $stmt->bindValue(($key+1), $value, \PDO::PARAM_INT);
            }
            else {
                $stmt->bindValue(($key+1), $value, \PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        $rows_amount = 0;
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rows_amount = intval($row['rows_amount']);
        }
        return $rows_amount;
    }
    
    public function getLastSelectRowsTotal()
    {
        $sql = 'SHOW META';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $rowTotoal = 0;
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if($row['Variable_name']=='total') {
                 $rowTotoal = $row['Value']; 
            }
        }
        return $rowTotoal;
    }
}