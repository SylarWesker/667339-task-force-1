<?php

namespace TaskForce\Logic;

class RefuseAction extends Action
{
    private const ACTION_NAME = 'refuse';
    private const ACTION_TITLE = 'Назначить исполнителем';

    public static function getName(): string
    {
        return self::ACTION_NAME;
    }

    public static function getTitle(): string
    {
        return self::ACTION_TITLE;
    }

    public static function canExecute(int $id_user, int $client_id, int $performer_id): bool
    {
        return !is_null($performer_id) && $id_user === $performer_id;
    }
}
