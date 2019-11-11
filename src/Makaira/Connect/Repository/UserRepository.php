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
use Makaira\Connect\Exception as ConnectException;

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

        return reset($user);
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
     * @param string $token
     *
     * @return string
     */
    public function getUserIdByToken($token)
    {
        $query = "SELECT * FROM `makaira_connect_usertoken` WHERE `TOKEN` = :token AND NOW() < `VALID_UNTIL`";
        $row = $this->database->query($query, ['token' => $token]);

        $userId = empty($row) ? false : $row[0]['USERID'];

        return $userId;
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
                    'username' => $user['OXUSERNAME'],
                    'password' => $user['OXPASSWORD'],
                    'salt' => $user['OXPASSSALT']
                ]);
                break;
            }
        }

        return $authorizedUser;
    }

    /**
     * @param string $token
     *
     * @return User
     */
    public function getAuthorizedUserByToken($token)
    {
        $authorizedUser = new User([
            'ok' => false
        ]);
        if ($userId = $this->getUserIdByToken($token)) {
            $user = $this->get($userId);
            if ($this->isAuthorized($user)) {
                $authorizedUser = new User([
                    'ok' => true,
                    'username' => $user['OXUSERNAME'],
                    'password' => $user['OXPASSWORD'],
                    'salt' => $user['OXPASSSALT']
                ]);
            }
        }

        return $authorizedUser;
    }

    public function addUserToken($userId, $token, $validity)
    {
        $sql = "INSERT INTO `makaira_connect_usertoken`
                VALUES (:userId, :token, :validUntil)
                ON DUPLICATE KEY UPDATE `TOKEN` = VALUES(`TOKEN`), `VALID_UNTIL` = VALUES(`VALID_UNTIL`)";

        $date = new \DateTime();
        $date->add(\DateInterval::createFromDateString($validity));
        $params = [
            'userId'     => $userId,
            'token'      => $token,
            'validUntil' => $date->format('Y-m-d H:i:s')
        ];

        try {
            $this->database->execute($sql, $params);
        } catch (ConnectException $ex) {
            return false;
        } catch (\Exception $ex) {
            return false;
        }

        return true;
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
