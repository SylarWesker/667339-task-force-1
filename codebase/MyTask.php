<?php

namespace TaskForce\Codebase;

class Actions
{
    const ACTION_CANCEL = 1;   // Отменить (Заказчик)
    const ACTION_RESPOND = 2;  // Откликнуться (Исполнитель)
    const ACTION_REFUSE = 3;   // Отказаться (Исполнитель)
    const ACTION_COMPLETE = 4; // Выполнено (Заказчик)
    const ACTION_APPOINT = 5;  // Назначить исполнителя (Заказчик)
}

class Status
{
    const STATUS_NEW = 1;       // Новое задание (создано Заказчиком)
    const STATUS_CANCELED = 2;  // Задание отменено (Заказчиком)
    const STATUS_WORKED = 3;    // В работе (уставливается после того как Заказчик выбрал откликнувшегося Исполнителя).
    const STATUS_COMPLETED = 4; // Задание выполнено (устанавливает Заказчик).
    const STATUS_FAILED = 5;    // Провалено (если Исполнитель взял в работу и отказался).
}

class TaskMyVersion
{
    protected $id_client;       // id заказчика.
    protected $id_performer;    // id исполнителя.
    protected $finish_date;     // срок завершения.
    protected $status;          // текущий статус.

    // Статус и действие связанные сущности?

    public function __construct($id_client, $finish_date) {
        $this->id_client = $id_client;
        $this->finish_date = $finish_date;

        $this->status = Status::STATUS_NEW;
    }

    // Возвращает список статусов.
    public function getStatusList() {
        return [
            Status::STATUS_NEW,
            Status::STATUS_CANCELED,
            Status::STATUS_WORKED,
            Status::STATUS_COMPLETED,
            Status::STATUS_FAILED,
        ];
    }

    // Возвращает список действий.
    public function getActionList() {
        return [
            Actions::ACTION_CANCEL,
            Actions::ACTION_RESPOND,
            Actions::ACTION_REFUSE,
            Actions::ACTION_COMPLETE,
            Actions::ACTION_APPOINT,
        ];
    }

    // Возвращает статус, в который перейдет задача после действия указанного в параметре.
    public function getNextStatus($action) {
        // Тут switch case с проверками?
    }
}

