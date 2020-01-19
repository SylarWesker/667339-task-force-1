<?php

namespace TaskForce\Logic;

/**
 * Class Task - класс задачи
 * @package TaskForce\Logic
 */
class Task
{
    public const STATUS_NEW = 'new';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_WORKED = 'worked';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public const ACTION_CANCEL = 'cancel';
    public const ACTION_RESPOND = 'respond';
    public const ACTION_REFUSE = 'refuse';
    public const ACTION_COMPLETE = 'complete';
    public const ACTION_APPOINT = 'appoint';

    /** @var int $client_id - id заказчика */
    protected $client_id;

    /** @var int $performer_id - id исполнителя */
    protected $performer_id;

    /** @var date $finish_date - срок завершения задачи */
    protected $finish_date;

    /** @var string $status - текущий статус задачи */
    protected $status = self::STATUS_NEW;

    // ToDo
    // а как же дата завершения? логичнее передавать id заказчика и дату завершения, а id исполнителя потом установить когда он станет известен.
    public function __construct($id_client, $id_performer)
    {
        $this->client_id = $id_client;
        $this->performer_id = $id_performer;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getPerformerId()
    {
        return $this->performer_id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusList()
    {
        return [
            self::STATUS_NEW => 'новое',
            self::STATUS_CANCELED => 'отменено',
            self::STATUS_WORKED => 'в работе',
            self::STATUS_COMPLETED => 'завершено',
            self::STATUS_FAILED => 'провалено',
        ];
    }

    public function getActionList()
    {
        return [
            self::ACTION_CANCEL => 'отменить',
            self::ACTION_RESPOND => 'откликнуться',
            self::ACTION_REFUSE => 'отказаться',
            self::ACTION_COMPLETE => 'выполнено',
            self::ACTION_APPOINT => 'назначить исполнителя',
        ];

        /*return [
            new CancelAction(),
            new RespondAction(),
            new RefuseAction(),
            new CompleteAction(),
            new AppointAction(),
        ];*/
    }

    // ToDo
    // Возможность применения действия также зависит от роли...
    /**
     * @param string $action действие
     * @return string статус в которое перейдет задача после указанного действия
     */
    public function getNextStatus($action)
    {
        $nextStatus = $this->status;

        switch ($action) {
            case self::ACTION_CANCEL:
            {
                if ($this->status == self::STATUS_NEW) {
                    $nextStatus = self::STATUS_CANCELED;
                }
                break;
            }
            case self::ACTION_REFUSE:
            {
                if ($this->status == self::STATUS_WORKED) {
                    $nextStatus = self::STATUS_FAILED;
                }
                break;
            }
            case self::ACTION_APPOINT:
            {
                if ($this->status == self::STATUS_NEW) {
                    $nextStatus = self::STATUS_WORKED;
                }
                break;
            }
            case self::ACTION_COMPLETE:
            {
                if ($this->status == self::STATUS_WORKED) {
                    $nextStatus = self::STATUS_COMPLETED;
                }
                break;
            }
        }

        return $nextStatus;
    }

    /**
     * Возвращает список, возможных действий для задачи.
     * @param $status - статус задачи
     * @param int $user_id - id пользователя
     * @return array список возможных действий для указанного статуса.
     */
    public function getActions($status, $user_id)
    {
        switch ($status) {
            case self::STATUS_NEW:
            {
                $actions = [new CancelAction(), new RespondAction(), new AppointAction()];
                break;
            }
            case self::STATUS_WORKED:
            {
                $actions = [new RefuseAction(), new CompleteAction()];
                break;
            }
            default:
            {
                $actions = [];
            }
        }

        if (!empty($actions)) {
            $actions = array_filter($actions, function ($x) use ($user_id) {
                return $x->canExecute($user_id, $this->client_id, $this->performer_id);
            });
        }

        return $actions;
    }
}
