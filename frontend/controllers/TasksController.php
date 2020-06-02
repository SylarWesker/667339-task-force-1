<?php

namespace frontend\controllers;

use frontend\models\Task;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $tasks = Task::find()->forTasksPageView()->all();

        return $this->render('index', ['tasks' => $tasks]);
    }
}
