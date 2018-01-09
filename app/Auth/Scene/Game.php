<?php

namespace App\Auth\Scene;


use App\Auth\Role\User;
use Hail\Auth\AbstractScene;
use Hail\Auth\RoleInterface;

class Game extends AbstractScene
{
    public function __construct($id)
    {
        $game = $this->cdb->name('game_' . $id)->select([
            'SELECT' => '*',
            'FROM' => 'games',
            'WHERE' => ['id' => $id],
        ]);

        if ($game === null) {
            throw new \RuntimeException('Game invalid: ' . $id);
        }

        parent::__construct($game);
    }

    public function in(RoleInterface $role): void
    {
        if (!$role instanceof User) {
            throw new \RuntimeException('Only user can in scene');
        }

        $groups = $role->getBelongToByType('group');
        if ($groups !== []) {
            $groupIds = [];
            foreach ($groups as $group) {
                $groupIds[] = $group->getId();
            }
            $gameId = $this->getId();
            $roleId = $role->getId();

            $managers = $this->cdb->name("game_{$gameId}_manager_{$roleId}")->select([
                'SELECT' => 'manager_id',
                'FROM' => 'game_managers',
                'WHERE' => ['game_id' => $gameId, 'group_id' => $groupIds],
            ]);

            foreach ($managers as ['manager_id' => $id]) {
                $role->addBelongTo(
                    $this->auth->getRole('manager', $id)
                );
            }
        }

        parent::in($role);
    }

    public function out(RoleInterface $role): void
    {
        $role->delBelongToByType('manager');

        parent::out($role);
    }

    public function rules()
    {
        $type = $this->getType();
        $id = $this->getId();

        $rules = $this->cdb->name("rules_{$type}_{$id}")->select([
            'SELECT' => ['name', 'allow', 'attribute', 'operation', 'value', 'priority'],
            'FROM' => 'authorizations',
            'WHERE' => ['type' => $type, 'id' => $id],
        ]);

        foreach ($rules as $row) {
            [
                'name' => $name,
                'allow' => $allow,
                'attribute' => $attr,
                'operation' => $op,
                'value' => $value,
                'priority' => $priority,
            ] = $row;

            $rule = $this->auth->createRule($name, (bool) $allow, $priority, $attr, $op, $value);
            $this->addRule($rule);
        }

        $rule = $this->auth->createRule('show', true, 0, 'manager::count', '>', 0);
        $this->addRule($rule);
    }
}