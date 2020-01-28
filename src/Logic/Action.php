<?php

namespace TaskForce\Logic;

abstract class Action
{
    /**
     * @return string - Возвращает внутренее имя действия.
     */
    abstract public static function getName(): string;

    /**
     * @return string - Возвращает название действия.
     */
    abstract public static function getTitle(): string;

    /**
     * Проверка прав на выполнение действия.
     * @param int $id_user - id текущего пользователя
     * @param int $client_id - id клиента
     * @param int $performer_id - id исполнителя
     * @return bool - может ли быть выполнено действие для задачи пользователем с id = $id_user
     */
    abstract public static function canExecute(int $id_user, int $client_id, int $performer_id): bool;
}
