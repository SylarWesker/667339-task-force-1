<?php

namespace TaskForce\Logic;

class CompleteAction extends Action
{
    private const ACTION_NAME = 'complete';

    public static function getName()
    {
        return 'Выполнить';
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
