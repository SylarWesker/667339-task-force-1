<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use TaskForce\Logic\Task;
use TaskForce\Logic\CancelAction;
use TaskForce\Logic\AppointAction;
use TaskForce\Logic\RespondAction;
use TaskForce\Logic\CompleteAction;
use TaskForce\Logic\RefuseAction;

// ToDo
// В psr написано "There MUST be one use keyword per declaration." т.е на каждый использование класса отдельный use
// т.е нельзя делать как в строке ниже? (точнее не рекомендуется). Если да, то почему?
// use TaskForce\Logic\{CancelAction, AppointAction, RespondAction, CompleteAction, RefuseAction};

final class TaskTest extends TestCase
{
    private $className = Task::class;

    protected $task;
    protected $id_client;
    protected $id_performer;

    protected function setUp(): void
    {
        $this->id_client = 1;
        $this->id_performer = 2;
        $this->task = new Task($this->id_client, $this->id_performer);
    }

    public function testGetNextStatusWithStatusNew()
    {
        // Статус = Новый по умолчанию у только что созданой задачи.
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_CANCEL) == Task::STATUS_CANCELED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_APPOINT) == Task::STATUS_WORKED);

        // все остальные действия не должны повлиять.
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_COMPLETE) == Task::STATUS_NEW);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_REFUSE) == Task::STATUS_NEW);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_RESPOND) == Task::STATUS_NEW);
    }

    public function testGetNextStatusWithStatusWorked()
    {
        // Т.к следующий статус зависит и от действия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $property->setValue($this->task, Task::STATUS_WORKED);

        $this->assertTrue($this->task->getNextStatus(Task::ACTION_COMPLETE) == Task::STATUS_COMPLETED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_REFUSE) == Task::STATUS_FAILED);

        // все остальные действия не должны повлиять.
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_RESPOND) == Task::STATUS_WORKED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_APPOINT) == Task::STATUS_WORKED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_CANCEL) == Task::STATUS_WORKED);
    }

    public function testGetNextStatusWithStatusComplete()
    {
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $property->setValue($this->task, Task::STATUS_COMPLETED);

        $this->assertTrue($this->task->getNextStatus(Task::ACTION_CANCEL) == Task::STATUS_COMPLETED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_REFUSE) == Task::STATUS_COMPLETED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_RESPOND) == Task::STATUS_COMPLETED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_APPOINT) == Task::STATUS_COMPLETED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_COMPLETE) == Task::STATUS_COMPLETED);
    }

    public function testGetNextStatusWithStatusFailed()
    {
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $property->setValue($this->task, Task::STATUS_FAILED);

        $this->assertTrue($this->task->getNextStatus(Task::ACTION_CANCEL) == Task::STATUS_FAILED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_REFUSE) == Task::STATUS_FAILED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_RESPOND) == Task::STATUS_FAILED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_APPOINT) == Task::STATUS_FAILED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_COMPLETE) == Task::STATUS_FAILED);
    }

    public function testGetNextStatusWithStatusCanceled()
    {
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
        // Пока не реализована смена статусов. Поэтому приходится менять через рефлексию.
        $reflector = new ReflectionClass($this->className);
        $property = $reflector->getProperty('status');
        $property->setAccessible(true);

        $property->setValue($this->task, Task::STATUS_CANCELED);

        $this->assertTrue($this->task->getNextStatus(Task::ACTION_CANCEL) == Task::STATUS_CANCELED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_REFUSE) == Task::STATUS_CANCELED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_RESPOND) == Task::STATUS_CANCELED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_APPOINT) == Task::STATUS_CANCELED);
        $this->assertTrue($this->task->getNextStatus(Task::ACTION_COMPLETE) == Task::STATUS_CANCELED);
    }

    public function testGetActionsWithNewStatusToClient()
    {
        $this->assertEqualsCanonicalizing($this->task->getActions(Task::STATUS_NEW, $this->id_client), [new CancelAction(), new AppointAction()]);
    }

    public function testGetActionsWithNewStatusToPerformer()
    {
        $this->assertEqualsCanonicalizing($this->task->getActions(Task::STATUS_NEW, $this->id_performer), [new RespondAction()]);
    }

    public function testGetActionsWithWorkedStatusToClient()
    {
        $this->assertEqualsCanonicalizing($this->task->getActions(Task::STATUS_WORKED, $this->id_client), [new CompleteAction()]);
    }

    public function testGetActionsWithWorkedStatusToPerformer()
    {
        $this->assertEqualsCanonicalizing($this->task->getActions(Task::STATUS_WORKED, $this->id_performer), [new RefuseAction()]);
    }

    public function testGetActionsWithOtherStatusToClient()
    {
        $this->assertEquals($this->task->getActions(Task::STATUS_CANCELED, $this->id_client), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_FAILED, $this->id_client), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_COMPLETED, $this->id_client), []);
    }

    public function testGetActionsWithOtherStatusToPerformer()
    {
        $this->assertEquals($this->task->getActions(Task::STATUS_CANCELED, $this->id_performer), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_FAILED, $this->id_performer), []);
        $this->assertEquals($this->task->getActions(Task::STATUS_COMPLETED, $this->id_performer), []);
    }
}
