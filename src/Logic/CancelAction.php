<?php

namespace TaskForce\Logic;

class CancelAction extends Action
{
    private const ACTION_NAME = 'cancel';

    public static function getName()
    {
        return 'Отменить';
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
