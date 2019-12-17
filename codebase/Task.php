<?php

namespace TaskForce\Codebase;

class Task
{
    protected $id_client;       // id заказчика.
    protected $id_performer;    // id исполнителя.
    protected $finish_date;     // срок завершения.
    protected $status;          // текущий статус.

    // Геттеры.
    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getIdPerformer()
    {
        return $this->id_performer;
    }

    public function getStatus()
    {
        return $this->status;
    }

    // Статусы.
    const STATUS_NEW        = 'new';         // Новое задание (создано Заказчиком)
    const STATUS_CANCELED   = 'canceled';    // Задание отменено (Заказчиком)
    const STATUS_WORKED     = 'worked';      // В работе (уставливается после того как Заказчик выбрал откликнувшегося Исполнителя).
    const STATUS_COMPLETED  = 'completed';   // Задание выполнено (устанавливает Заказчик).
    const STATUS_FAILED     = 'failed';      // Провалено (если Исполнитель взял в работу и отказался).

    // Действия.
    const ACTION_CANCEL     = 'cancel';     // Отменить (Заказчик)
    const ACTION_RESPOND    = 'respond';    // Откликнуться (Исполнитель)
    const ACTION_REFUSE     = 'refuse';     // Отказаться (Исполнитель)
    const ACTION_COMPLETE   = 'complete';   // Выполнено (Заказчик)
    const ACTION_APPOINT    = 'appoint';    // Назначить исполнителя (Заказчик)

    // ToDo
    // а как же дата завершения? логичнее передавать id заказчика и дату завершения, а id исполнителя потом установить когда он станет известен.
    public function __construct($id_client, $id_performer) {
        // ToDo
        // По идее id клиента не может быть равно id исполнителя.
        // Но кидать исключение в конструкторе вроде плохой тон.
        $this->id_client = $id_client;
        $this->id_performer = $id_performer;

        $this->status = self::STATUS_NEW;
    }

    // Возвращает список статусов.
    public function getStatusList() {
        return [
            self::STATUS_NEW        => 'новое',
            self::STATUS_CANCELED   => 'отменено',
            self::STATUS_WORKED     => 'в работе',
            self::STATUS_COMPLETED  => 'завершено',
            self::STATUS_FAILED     => 'провалено',
        ];
    }

    // Возвращает список действий.
    public function getActionList() {
        return [
            self::ACTION_CANCEL     => 'отменить',
            self::ACTION_RESPOND    => 'откликнуться',
            self::ACTION_REFUSE     => 'отказаться',
            self::ACTION_COMPLETE   => 'выполнено',
            self::ACTION_APPOINT    => 'назначить исполнителя',
        ];
    }

    // ToDo
    // Возможность применения действия также зависит от роли...
    // Возвращает статус, в который перейдет задача после указанного действия.
    public function getNextStatus($action) {
        $nextStatus = $this->status;

        switch ($action) {
            case self::ACTION_CANCEL: {
                if ($this->status == self::STATUS_NEW) {
                    $nextStatus = self::STATUS_CANCELED;
                }
                break;
            }
            case self::ACTION_RESPOND: {
                if ($this->status == self::STATUS_NEW) {
                    // ToDo
                    // То что кто-то откликнулся еще не значит, что он сразу становится исполнителем. Исполнителя должен утвердить заказчик.
                    // т.е получается ничего не меняется???
                    $nextStatus = $this->status;
                }
                break;
            }
            case self::ACTION_REFUSE: {
                if ($this->status == self::STATUS_WORKED) {
                    $nextStatus = self::STATUS_FAILED;
                }
                break;
            }
            case self::ACTION_APPOINT: {
                if ($this->status == self::STATUS_NEW) {
                    $nextStatus = self::STATUS_WORKED;
                }
                break;
            }
            case self::ACTION_COMPLETE: {
                if ($this->status == self::STATUS_WORKED) {
                    $nextStatus = self::STATUS_COMPLETED;
                }
                break;
            }
        }

        return $nextStatus;
    }

    // ToDo
    // Список действий также зависит от роли...
    // Возвращает список возможных действий для указанного статуса.
    public function getActions($status) {
        $availableActions = [];

        switch ($status) {
            case self::STATUS_NEW: {
                $availableActions = [ self::ACTION_CANCEL, self::ACTION_RESPOND, self::ACTION_APPOINT ];
                break;
            }
            case self::STATUS_WORKED: {
                $availableActions = [ self::ACTION_REFUSE, self::ACTION_COMPLETE ];
                break;
            }
            case self::STATUS_COMPLETED:
            case self::STATUS_FAILED:
            case self::STATUS_CANCELED: {
                // ничего нельзя сделать
                break;
            }
        }

        return $availableActions;
    }
}
