<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use TaskForce\Logic\Task;
use TaskForce\Logic\CancelAction;
use TaskForce\Logic\AppointAction;
use TaskForce\Logic\RespondAction;
use TaskForce\Logic\CompleteAction;
use TaskForce\Logic\RefuseAction;
use TaskForce\Exception\WrongStatusException;

// ToDo
// В psr написано "There MUST be one use keyword per declaration." т.е на каждый использование класса отдельный use
// т.е нельзя делать как в строке ниже? (точнее не рекомендуется). Если да, то почему?
// use TaskForce\Logic\{CancelAction, AppointAction, RespondAction, CompleteAction, RefuseAction};

final class TaskTest extends TestCase
{
    private string $className = Task::class;

    protected Task $task;
    protected int $client_id;
    protected int $performer_id;

    protected function setUp(): void
    {
        $this->client_id = 1;
        $this->performer_id = 2;
        $this->task = new Task($this->client_id, $this->performer_id);
    }

    public function testGetNextStatusWithStatusNew()
    {
        // Статус = Новый (по умолчанию у только что созданой задачи).
        $currentStatus = Task::STATUS_NEW;

        // Эти действия может выполнить только создатель задачи.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == Task::STATUS_CANCELED);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == Task::STATUS_WORKED);

        // Исполнитель не может выполнить действия и следовательно переключить статус задачи.
        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);

        // все остальные действия не должны повлиять.
        // Причем не важно кто из пытается выполнить.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);

        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
    }

    public function testGetNextStatusWithStatusWorked()
    {
        // Т.к следующий статус зависит и от действия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $currentStatus = Task::STATUS_WORKED;
        $property->setValue($this->task, $currentStatus);

        // Только клиент-заказчик определяет, что задача завершена.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == Task::STATUS_COMPLETED);

        // Только исполнитель может отказаться от выполнения задачи, которая уже в работе.
        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == Task::STATUS_FAILED);

        // все остальные действия не должны повлиять.
        // Причем не важно кто из пытается выполнить.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);

        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
    }

    public function testGetNextStatusWithStatusComplete()
    {
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $currentStatus = Task::STATUS_COMPLETED;
        $property->setValue($this->task, $currentStatus);

        // На закрытые задачи уже никто и никак не может повлиять.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);

        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);
    }

    public function testGetNextStatusWithStatusFailed()
    {
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $currentStatus = Task::STATUS_FAILED;
        $property->setValue($this->task, $currentStatus);

        // На проваленные задачи уже никто и никак не может повлиять.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);

        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);
    }

    public function testGetNextStatusWithStatusCanceled()
    {
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $currentStatus = Task::STATUS_CANCELED;
        $property->setValue($this->task, $currentStatus);

        // На отмененные задачи уже никто и никак не может повлиять.
        $user_id = $this->client_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);

        // по идее у отмененных и исполнителя быть не может, но пусть в тесте будет.
        $user_id = $this->performer_id;
        $this->assertTrue($this->task->getNextStatus(new CancelAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RefuseAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new RespondAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new AppointAction(), $user_id) == $currentStatus);
        $this->assertTrue($this->task->getNextStatus(new CompleteAction(), $user_id) == $currentStatus);
    }

    public function testGetActionsWithNewStatusToClient()
    {
        $this->assertEqualsCanonicalizing(
            $this->task->getActions(Task::STATUS_NEW, $this->client_id),
            [new CancelAction(), new AppointAction()]
        );
    }

    public function testGetActionsWithNewStatusToPerformer()
    {
        $this->assertEqualsCanonicalizing(
            $this->task->getActions(Task::STATUS_NEW, $this->performer_id),
            [new RespondAction()]
        );
    }

    public function testGetActionsWithWorkedStatusToClient()
    {
        $this->assertEqualsCanonicalizing(
            $this->task->getActions(Task::STATUS_WORKED, $this->client_id),
            [new CompleteAction()]
        );
    }

    public function testGetActionsWithWorkedStatusToPerformer()
    {
        $this->assertEqualsCanonicalizing(
            $this->task->getActions(Task::STATUS_WORKED, $this->performer_id),
            [new RefuseAction()]
        );
    }

    public function testGetActionsWithOtherStatusToClient()
    {
        $this->assertEquals($this->task->getActions(Task::STATUS_CANCELED, $this->client_id), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_FAILED, $this->client_id), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_COMPLETED, $this->client_id), []);
    }

    public function testGetActionsWithOtherStatusToPerformer()
    {
        $this->assertEquals($this->task->getActions(Task::STATUS_CANCELED, $this->performer_id), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_FAILED, $this->performer_id), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_COMPLETED, $this->performer_id), []);
    }

    public function testGetActionsWrongStatus()
    {
        $this->expectException(WrongStatusException::class);
        $this->task->getActions('NotExistStatus', $this->client_id);
    }
}
