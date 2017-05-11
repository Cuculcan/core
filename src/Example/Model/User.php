<?php

namespace Example\Model;

use Cuculcan\Core\Model\Entity;

class User extends Entity
{

    public function __construct($id, $name, $email, $phone)
    {
        $this->fields = [];
        $this->fields['id'] = $id;
        $this->fields['name'] = $name;
        $this->fields['email'] = $email;
        $this->fields['phone'] = $phone;
    }

}
