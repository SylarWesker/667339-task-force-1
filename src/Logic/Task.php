<?php

namespace TaskForce\Logic;

use DateTime;
use TaskForce\Exception\WrongStatusException;

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

    /** @var int $client_id - id заказчика */
    protected int $client_id;

    /** @var int $performer_id - id исполнителя */
    protected int $performer_id;

    /** @var DateTime $finish_date - срок завершения задачи */
    protected DateTime $finish_date;

    /** @var string $status - текущий статус задачи */
    protected string $status = self::STATUS_NEW;

    public function __construct(int $id_client, int $id_performer)
    {
        $this->client_id = $id_client;
        $this->performer_id = $id_performer;
    }

    public function getClientId(): int
    {
        return $this->client_id;
    }

    public function getPerformerId(): int
    {
        return $this->performer_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusList(): array
    {
        return [
            self::STATUS_NEW => 'новое',
            self::STATUS_CANCELED => 'отменено',
            self::STATUS_WORKED => 'в работе',
            self::STATUS_COMPLETED => 'завершено',
            self::STATUS_FAILED => 'провалено',
        ];
    }

    public function getActionList(): array
    {
        return [
            RespondAction::getName() => RespondAction::getTitle(),
            RefuseAction::getName() => RefuseAction::getTitle(),
            CancelAction::getName() => CancelAction::getTitle(),
            CompleteAction::getName() => CompleteAction::getTitle(),
            AppointAction::getName() => AppointAction::getTitle(),
        ];
    }

    /**
     * @param Action $action действие
     * @param int $user_id id пользователя
     * @return string статус в которое перейдет задача после указанного действия
     */
    public function getNextStatus(Action $action, int $user_id): string
    {
        $nextStatus = $this->status;

        // Если действие не может быть выполнено текущим пользователем, то нет смысла дальше проверять.
        if (!$action->canExecute($user_id, $this->client_id, $this->performer_id)) {
            return $nextStatus;
        }

        switch ($action->getName()) {
            case CancelAction::getName():
            {
                if ($this->status == self::STATUS_NEW) {
                    $nextStatus = self::STATUS_CANCELED;
                }
                break;
            }
            case RefuseAction::getName():
            {
                if ($this->status == self::STATUS_WORKED) {
                    $nextStatus = self::STATUS_FAILED;
                }
                break;
            }
            case AppointAction::getName():
            {
                if ($this->status == self::STATUS_NEW) {
                    $nextStatus = self::STATUS_WORKED;
                }
                break;
            }
            case CompleteAction::getName():
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
     * @param string $status - статус задачи
     * @param int $user_id - id пользователя
     * @return array список возможных действий для указанного статуса.
     * @throws WrongStatusException
     */
    public function getActions(string $status, int $user_id): array
    {
        $status_list = $this->getStatusList();
        if (!array_key_exists($status, $status_list)) {
            throw new WrongStatusException('Не существует статуса ' . $status);
        }

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
            $actions = array_filter(
                $actions,
                function (Action $action) use ($user_id) {
                    return $action->canExecute($user_id, $this->client_id, $this->performer_id);
                }
            );
        }

        return $actions;
    }
}
