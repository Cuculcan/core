<?php


namespace Cuculcan\Core\Repositories;

use Cuculcan\Core\Repositories\Criteria;

class SearchParams
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     *
     * @var Criteria 
     */
    private $criterias;

    private $orders;

    private $limit;
    
    public function __construct()
    {
        $this->criterias = [];
        $this->orders = [];
        $this->limit = '';
    }
    
    public function add(Criteria $criteria)
    {
        $this->criterias[] = $criteria;
    }

    public function addOrder($field, $direction)
    {
        $this->orders[] = $field.' '.$direction;
    }

    public function setLimit($start, $limit)
    {
        $start = (int)$start;
        $limit = (int)$limit;
        $this->limit = 'LIMIT '.$start.', '.$limit;
    }

    public function build()
    {
        $criterias = [];
        $values = [];

        foreach($this->criterias AS $criteria) {
            $criteriaValue = $criteria->build();
            $criterias[] = $criteriaValue['sql'];
            $values = array_merge($values, $criteriaValue['value']);
        }

        $where = ' WHERE '.implode(' AND ', $criterias);

        if(count($this->orders)>0) {
            $where = $where. ' ORDER BY ' .implode(', ', $this->orders);
        }

        $where .= ' '.$this->limit;

        return [
            'sql' => $where,
            'values' => $values
        ];
    }
}
