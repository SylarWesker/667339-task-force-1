<?php

/* @var $this yii\web\View */
/* @var $users[] frontend\models\User */

$this->title = 'Исполнители';
$maxRating = 5;
?>

<section class="user__search">
    <div class="user__search-link">
        <p>Сортировать по:</p>
        <ul class="user__search-list">
            <li class="user__search-item user__search-item--current">
                <a href="#" class="link-regular">Рейтингу</a>
            </li>
            <li class="user__search-item">
                <a href="#" class="link-regular">Числу заказов</a>
            </li>
            <li class="user__search-item">
                <a href="#" class="link-regular">Популярности</a>
            </li>
        </ul>
    </div>

    <?php foreach ($users as $user): ?>
        <div class="content-view__feedback-card user__search-wrapper">
            <div class="feedback-card__top">
                <div class="user__search-icon">
                   <a href="#"><img src="./img/man-glasses.jpg" width="65" height="65"></a> <!-- "./img/man-glasses.jpg"-->

                    <!-- ToDo правильно скланять!  -->
                    <span><?= $user->getTasksAsPerformer()->count() ?> заданий</span>
                    <span><?= $user->reviewsCount ?> отзывов</span>
                </div>
                <div class="feedback-card__top--name user__search-card">
                    <p class="link-name"><a href="#" class="link-regular"><?= $user->full_name ?></a></p>

                    <!-- Рейтинг в виде звездочек -->
                    <?php for($i = 0; $i < floor($user->rating); $i++): ?>
                        <span></span>
                    <?php endfor; ?>
                    <?php for($i = floor($user->rating); $i < $maxRating; $i++): ?>
                        <span class="star-disabled"></span>
                    <?php endfor; ?>

                    <b><?= round($user->rating, 2) ?></b>
                    <p class="user__search-content">
                        <?= $user->profile->about ?>
                    </p>
                </div>

                <!-- ToDo пока не знаю как получить это значение! -->
                <span class="new-task__time">Был на сайте 25 минут назад</span>
            </div>
            <div class="link-specialization user__search-link--bottom">
                <?php foreach ($user->userSpecializations as $specialization): ?>
                    <?php if (!is_null($specialization)): ?>
                        <a href="#" class="link-regular"><?= $specialization->category->name ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach;?>

</section>

