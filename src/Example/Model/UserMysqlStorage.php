<?php

namespace Example\Model;

use Cuculcan\Core\Model\MysqlStorage;
use Cuculcan\Core\Model\Interfaces\EntityInterface;

class UserMysqlStorage extends MysqlStorage
{

    public function __construct($connection)
    {
        parent::__construct($connection, "Users");
    }

    public function add(EntityInterface $item)
    {
        try {
            $id = $item->get("id");
            if ($id > 0) {
                $this->update($item);
                return $id;
            }
        } catch (Exception $exc) {
            
        }
        return parent::add($item);
    }

    public function getById($id)
    {
        $data = parent::getById($id);
        
        if(!$data){
            return null;
        }
        $user = new User(
                $data['id'], 
                $data['name'],
                $data['email'],
                $data['phone']
        );
        return $user;
    }

}
