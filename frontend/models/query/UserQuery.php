<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Получаем только исполнителей.
     *
     * {@inheritdoc}
     * @return UserQuery
     */
    public function onlyPerfomers($db = null)
    {
        return parent::leftJoin('task','task.performer_id = user.id')
                       ->where(['not', ['task.performer_id' => null]]);
    }

    /**
     * Получаем только исполнителей (исполнителем считается тот у кого отмеченная хотя бы одна специализация).
     *
     * {@inheritdoc}
     * @return UserQuery
     */
    public function onlyPerfomers2($db = null)
    {
        return parent::leftJoin('user_specialization','user_specialization.user_id = user.id')
                        ->where(['not', ['task.category_id' => null]]);
    }
}
