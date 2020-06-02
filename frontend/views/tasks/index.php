<?php

use Carbon\Carbon;
use Carbon\CarbonInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $tasks [] frontend\models\Task */

$this->title = 'Новые задания';
?>

<section class="new-task">
    <div class="new-task__wrapper">
        <h1>Новые задания</h1>

        <?php foreach ($tasks as $task): ?>
            <div class="new-task__card">
                <div class="new-task__title">
                    <a href="#" class="link-regular"><h2><?= $task->description ?></h2></a>
                    <a class="new-task__type link-regular" href="#"><p><?= $task->category->name ?></p></a>
                </div>

                <?= Html::tag('div', '', ['class' => ['new-task__icon', 'new-task__icon--' . $task->category->icon_name]]) ?>

                <p class="new-task_description">
                    <?= $task->details ?>
                </p>
                <b class="new-task__price new-task__price--translation"><?= $task->budget ?><b> ₽</b></b>
                <p class="new-task__place"><?= $task->address ?></p>
                <span
                    class="new-task__time"><?= Carbon::now()->locale('ru_RU')->diffForHumans(new Carbon($task->creation_date), CarbonInterface::DIFF_ABSOLUTE) ?> назад</span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
