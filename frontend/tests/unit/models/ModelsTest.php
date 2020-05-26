<?php

namespace frontend\tests\unit\models;

use frontend\models\Category;
use frontend\models\Task;
use frontend\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindModels()
    {
        $user = User::findOne(1);
        $this->assertTrue($user->full_name == 'Karrie Buttress');

        $category = Category::findOne(1);
        $this->assertTrue($category->name == 'Курьерские услуги');
    }

    public function testTask()
    {
        $task = Task::findOne(1);

        $this->assertTrue($task->id == 1);
        $this->assertTrue($task->category->name == 'Уборка');
        $this->assertTrue($task->client->full_name == 'Boonie Terbeck');
        $this->assertTrue($task->performer->full_name == 'Matilde Pimblott');
    }

    public function testRelations()
    {
        $user = User::findOne(4);
        $this->assertTrue(count($user->tasksAsCreator) == 2);
        $this->assertTrue(count($user->tasksAsPerformer) == 1);
    }
}
