<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[FavoritePerformer]].
 *
 * @see FavoritePerformer
 */
class FavoritePerformerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return FavoritePerformer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return FavoritePerformer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
