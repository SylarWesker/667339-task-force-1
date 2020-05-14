<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Locality]].
 *
 * @see Locality
 */
class LocalityQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Locality[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Locality|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
