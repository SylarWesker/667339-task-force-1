<?php

namespace TaskForce\Logic;

class RefuseAction extends Action
{
    private const ACTION_NAME = 'refuse';

    public static function getName()
    {
        return 'Отказаться';
    }

    public static function getTitle()
    {
        return self::ACTION_NAME;
    }

    public static function canExecute($id_user, $client_id, $performer_id)
    {
        return !is_null($performer_id) && $id_user === $performer_id;
    }
}
