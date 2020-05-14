<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "task_related_file".
 *
 * @property int $id
 * @property int $task_id
 * @property string $filepath
 *
 * @property Task $task
 */
class TaskRelatedFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_related_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'filepath'], 'required'],
            [['task_id'], 'integer'],
            [['filepath'], 'string', 'max' => 255],
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
            'task_id' => 'Task ID',
            'filepath' => 'Filepath',
        ];
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
     * @return TaskRelatedFileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskRelatedFileQuery(get_called_class());
    }
}
