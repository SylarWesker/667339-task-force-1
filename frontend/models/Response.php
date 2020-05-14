<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "response".
 *
 * @property int $id
 * @property string|null $add_date
 * @property int $candidate_id
 * @property int $task_id
 * @property int|null $offered_price
 * @property string|null $comment
 *
 * @property User $candidate
 * @property Task $task
 */
class Response extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'response';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['add_date'], 'safe'],
            [['candidate_id', 'task_id'], 'required'],
            [['candidate_id', 'task_id', 'offered_price'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['task_id', 'candidate_id'], 'unique', 'targetAttribute' => ['task_id', 'candidate_id']],
            [['candidate_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['candidate_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'add_date' => 'Add Date',
            'candidate_id' => 'Candidate ID',
            'task_id' => 'Task ID',
            'offered_price' => 'Offered Price',
            'comment' => 'Comment',
        ];
    }

    /**
     * Gets query for [[Candidate]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCandidate()
    {
        return $this->hasOne(User::className(), ['id' => 'candidate_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery|TaskQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * {@inheritdoc}
     * @return ResponseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ResponseQuery(get_called_class());
    }
}
