<?php

namespace TaskForce\Logic;

class RespondAction extends Action
{
    private const ACTION_NAME = 'respond';
    private const ACTION_TITLE = 'Откликнуться';

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
        return $id_user !== $client_id;
    }
}
