<?php

namespace App\Auth\Role;


use Hail\Auth\AbstractRole;

class Manager extends AbstractRole
{
    public function __construct($id)
    {
        $data = $this->cdb->name('manager_' . $id)
            ->select([
                'SELECT' => '*',
                'FROM' => 'managers',
                'WHERE' => ['id' => $id]
            ]);

        if ($data === null) {
            throw new \RuntimeException('User not found: ' . $id);
        }

        parent::__construct($data);
    }
}