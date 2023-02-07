<?php

namespace Manager;

class User
{
    const limit = 10;

    /**
     * @var \Gateway\User $userGateway
     */
    protected static \Gateway\User $userGateway;

    public function __construct($userGateway)
    {
        self::$userGateway = $userGateway;
    }

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    function getUsers(int $ageFrom): array
    {
        return self::$userGateway::getUsers($ageFrom, self::limit);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @param array $names
     * @return array
     */
    public function getByNames(array $names): array
    {
        $users = [];
        foreach ($names as $name) {
            $users[] = self::$userGateway::user($name);
        }

        return $users;
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param array $users
     * @return array
     */
    public function users(array $users): array
    {
        $ids = [];
        try {
            self::$userGateway::getInstance()->beginTransaction();
            foreach ($users as $user) {
                self::$userGateway::add($user['name'], $user['lastName'], $user['age']);
                $ids[] = self::$userGateway::getInstance()->lastInsertId();
            }
            self::$userGateway::getInstance()->commit();
        } catch (\Exception $e) {
            self::$userGateway::getInstance()->rollBack();
            $ids = [];
        }


        return $ids;
    }
}
