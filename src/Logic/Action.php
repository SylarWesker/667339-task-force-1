<?php

namespace TaskForce\Logic;

abstract class Action
{
    // Возвращает название действия.
    public static abstract function getName();

    // Вовзращает внутренее имя.
    public static abstract function getTitle();

    /**
     * Проверка прав на выполнение действия.
     * @param $id_user - id текущего пользователя
     * @param $client_id - id клиента
     * @param $performer_id - id исполнителя
     * @return bool
     */
    public static abstract function canExecute($id_user, $client_id, $performer_id);
}
