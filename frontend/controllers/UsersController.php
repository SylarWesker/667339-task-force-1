<?php

namespace frontend\controllers;

use frontend\models\User;
use yii\web\Controller;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $users = User::find()->select(['user.*', 'rating' => 'AVG(review.rate)', 'reviewsCount' => 'COUNT(review.id)'])
                            ->onlyPerfomers()
                            ->leftJoin('review','review.task_id = task.id')
                            ->with(['userSpecializations.category'])
                            ->groupBy('user.id')
                            ->orderBy(['add_data' => SORT_DESC])  // ToDo! изменить на created_at
                            ->all();

        return $this->render('index', [ 'users' => $users ]);
    }
}
