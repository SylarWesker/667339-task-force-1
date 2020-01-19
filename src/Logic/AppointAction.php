<?php

namespace TaskForce\Logic;

class AppointAction
{
    private const ACTION_NAME = 'appoint';

    public static function getName()
    {
        return 'Назначить исполнителем';
    }

    public static function getTitle()
    {
        return self::ACTION_NAME;
    }

    public static function canExecute($id_user, $client_id, $performer_id)
    {
        return $id_user === $client_id;
    }
}
