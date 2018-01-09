<?php

namespace App\Auth\Role;

use Hail\Auth\AbstractRole;

class Group extends AbstractRole
{
    public function __construct($id)
    {
        $data = $this->cdb->name('group_' . $id)
            ->select([
                'SELECT' => '*',
                'FROM' => 'groups',
                'WHERE' => ['id' => $id]
            ]);

        if ($data === null) {
            throw new \RuntimeException('Group not found: ' . $id);
        }

        parent::__construct($data);
    }
}