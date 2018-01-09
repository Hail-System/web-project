<?php

namespace App\Auth\Scene;

use App\Auth\Role\User;
use Hail\Auth\AbstractScene;
use Hail\Auth\RoleInterface;

class Scene extends AbstractScene
{
    public const LOGIN = 'login',
        PANEL = 'panel',
        GAME = 'game',
        ADMIN = 'admin';

    public function __construct($id)
    {
        if (!\in_array($id, [self::LOGIN, self::PANEL, self::ADMIN], true)) {
            throw new \RuntimeException('Scene invalid: ' . $id);
        }

        parent::__construct($id);
    }

    public function in(RoleInterface $role): void
    {
        if (!$role instanceof User) {
            throw new \RuntimeException('Only user can in scene');
        }

        parent::in($role);
    }

    public function rules()
    {
        $id = $this->getId();

        switch ($id) {
            case self::LOGIN:
                $rule = $this->auth->createRule('*', true);
                $this->addRule($rule);
                break;

            case self::PANEL:
                $rule = $this->auth->createRule('*', true, 0, 'this:id', '>', 0);
                $this->addRule($rule);
                break;

            case self::ADMIN:
                $rule = $this->auth->createRule('*', true, 0, 'group:is_admin', '=', 1);
                $this->addRule($rule);
                break;
        }
    }
}