<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Repository;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Result\User;

class UserRepository
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * UserRepository constructor.
     *
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database  = $database;
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function get($id)
    {
        $query = "SELECT * FROM `oxuser` WHERE `OXID` = :id";
        $user = $this->database->query($query, ['id' => $id]);

        return $user;
    }

    /**
     * @param string $username
     *
     * @return array
     */
    public function getByUsername($username)
    {
        $query = "SELECT * FROM `oxuser` WHERE `OXUSERNAME` = :username";
        $userList = $this->database->query($query, ['username' => $username]);

        return $userList;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function getAuthorizedUserByUsername($username)
    {
        $authorizedUser = new User([
            'ok' => false
        ]);
        $userList = $this->getByUsername($username);
        foreach ($userList as $user) {
            if ($this->isAuthorized($user)) {
                $authorizedUser = new User([
                    'ok' => true,
                    'password' => $user['OXPASSWORD'],
                    'salt' => $user['OXPASSSALT']
                ]);
                break;
            }
        }
        return $authorizedUser;
    }

    /**
     * @param array $user
     *
     * @return bool
     */
    private function isAuthorized($user)
    {
        return ($user['OXACTIVE'] === 1) && ($user['OXRIGHTS'] == 'malladmin');
    }
}
