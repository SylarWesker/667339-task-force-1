<?php

namespace TaskForce\Logic;

class RespondAction extends Action
{
    private const ACTION_NAME = 'respond';

    public static function getName()
    {
        return 'Откликнуться';
    }

    public static function getTitle()
    {
        return self::ACTION_NAME;
    }

    public static function canExecute($id_user, $client_id, $performer_id)
    {
        return $id_user !== $client_id;
    }
}
