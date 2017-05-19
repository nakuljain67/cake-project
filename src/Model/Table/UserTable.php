<?php

use Cake\ORM\Query;
use Cake\ORM\Table;

class UserTable extends Table {

    public function getUsers() {
        $query = TableRegistry::get('tbl_users');
        $results = $query->find();
        return $results;
    }

}
?>