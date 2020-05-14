<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UserSpecialization]].
 *
 * @see UserSpecialization
 */
class UserSpecializationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserSpecialization[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserSpecialization|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
