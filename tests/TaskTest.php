<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use TaskForce\Logic\Task;

final class TaskTest extends TestCase
{
    protected $task;
    private $className = Task::class;

    protected function setUp(): void
    {
        $this->task = new Task(1,2);
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
        // Т.к следующий статус зависит и от дейтсвия и от текущего статуса, то нужен способ задать статус.
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

    public function testGetActionsWithNewStatus()
    {
        $this->assertEquals($this->task->getActions(Task::STATUS_NEW), [ Task::ACTION_CANCEL, Task::ACTION_RESPOND, Task::ACTION_APPOINT]);
    }

    public function testGetActionsWithWorkedStatus()
    {
        $this->assertEquals($this->task->getActions(Task::STATUS_WORKED), [ Task::ACTION_REFUSE, Task::ACTION_COMPLETE ]);
    }

    public function testGetActionsWithOtherStatus()
    {
        $this->assertEquals($this->task->getActions(Task::ACTION_CANCEL), [ ]);
        $this->assertEquals($this->task->getActions(Task::ACTION_REFUSE), [ ]);
        $this->assertEquals($this->task->getActions(Task::ACTION_COMPLETE), [ ]);
    }
}
