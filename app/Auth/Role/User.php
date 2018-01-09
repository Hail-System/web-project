<?php

namespace App\Auth\Role;

use App\ErrorCode;
use App\Auth\Exception\EntryException;
use Hail\Auth\AbstractRole;
use Hail\Auth\RoleInterface;
use Hail\Auth\SceneInterface;

class User extends AbstractRole
{
    public function __construct($id)
    {
        if ($id > 0) {
            $data = $this->cdb->name('user_' . $id)
                ->select([
                    'SELECT' => '*',
                    'FROM' => 'users',
                    'WHERE' => ['id' => $id]
                ]);

            if ($data === null) {
                throw new \RuntimeException('User not found: ' . $id);
            }
        }

        parent::__construct($data);
    }

    public function belongTo($name)
    {
        $id = $this->getId();

        if ($id > 0) {
            $groupIds = $this->cdb->name('group_users_' . $id)->select([
                'SELECT' => ['group_id'],
                'FROM' => 'group_users',
                'WHERE' => ['user_id' => $id]
            ]);

            foreach ($groupIds as $groupId) {
                $this->addBelongTo(
                    $this->auth->getRole($name, $groupId)
                );
            }
        }
    }

    public function entry(SceneInterface $to): RoleInterface
    {
        if ($this->getId() === 0) {
            $scene = $this->getScene();
            if ($scene && $scene->is('scene', 'login')) {
                $username = $this->request->input('username');

                $data = $this->db->get([
                    'SELECT' => ['id', 'password'],
                    'FROM' => 'users',
                    'WHERE' => ['name' => $username],
                ]);

                if (empty($data)) {
                    throw new EntryException(_('用户未找到'), ErrorCode::USER_NOT_FOUND);
                }

                $password = $this->request->input('password');

                $check = $this->crypto->verifyPassword($password, $data['password']);
                if ($check === false) {
                    throw new EntryException(_('密码验证失败'), ErrorCode::PASSWORD_INVALID);
                }

                if (\is_string($check)) {
                    $this->db->update('users', ['password' => $check], ['id' => $data['id']]);
                }

                $this->session->set('login', [
                    'id' => $data['id'],
                    'name' => $username,
                ]);
            }

            if (($id = $this->session->get('login.id')) > 0) {
                if ($scene) {
                    $scene->out($this);
                }

                $user = $this->auth->getRole('user', $id);

                return $user->entry($to);
            }
        }

        return parent::entry($to);
    }
}