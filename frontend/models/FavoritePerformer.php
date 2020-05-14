<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "favorite_performer".
 *
 * @property int $id
 * @property int $client_id
 * @property int $performer_id
 *
 * @property User $client
 * @property User $performer
 */
class FavoritePerformer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorite_performer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'performer_id'], 'required'],
            [['client_id', 'performer_id'], 'integer'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['performer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'performer_id' => 'Performer ID',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getClient()
    {
        return $this->hasOne(User::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getPerformer()
    {
        return $this->hasOne(User::className(), ['id' => 'performer_id']);
    }

    /**
     * {@inheritdoc}
     * @return FavoritePerformerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FavoritePerformerQuery(get_called_class());
    }
}
