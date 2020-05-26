<?php

namespace frontend\models;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $client_id
 * @property int|null $performer_id
 * @property int|null $status_id
 * @property int $category_id
 * @property string $description
 * @property string $details
 * @property int|null $budget
 * @property string|null $creation_date
 * @property string|null $finish_date
 * @property string|null $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $locality_id
 *
 * @property Message[] $messages
 * @property Response[] $responses
 * @property User[] $candidates
 * @property Review[] $reviews
 * @property User $client
 * @property User $performer
 * @property Category $category
 * @property TaskStatus $status
 * @property Locality $locality
 * @property TaskRelatedFile[] $taskRelatedFiles
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'category_id', 'description', 'details'], 'required'],
            [['client_id', 'performer_id', 'status_id', 'category_id', 'budget', 'locality_id'], 'integer'],
            [['details'], 'string'],
            [['creation_date', 'finish_date'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['description', 'address'], 'string', 'max' => 255],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['performer_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['locality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locality::className(), 'targetAttribute' => ['locality_id' => 'id']],
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
            'status_id' => 'Status ID',
            'category_id' => 'Category ID',
            'description' => 'Description',
            'details' => 'Details',
            'budget' => 'Budget',
            'creation_date' => 'Creation Date',
            'finish_date' => 'Finish Date',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'locality_id' => 'Locality ID',
        ];
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery|MessageQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery|ResponseQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Response::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Candidates]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCandidates()
    {
        return $this->hasMany(User::className(), ['id' => 'candidate_id'])->viaTable('response', ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery|ReviewQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::className(), ['task_id' => 'id']);
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
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery|CategoryQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery|TaskStatusQuery
     */
    public function getStatus()
    {
        return $this->hasOne(TaskStatus::className(), ['id' => 'status_id']);
    }

    /**
     * Gets query for [[Locality]].
     *
     * @return \yii\db\ActiveQuery|LocalityQuery
     */
    public function getLocality()
    {
        return $this->hasOne(Locality::className(), ['id' => 'locality_id']);
    }

    /**
     * Gets query for [[TaskRelatedFiles]].
     *
     * @return \yii\db\ActiveQuery|TaskRelatedFileQuery
     */
    public function getTaskRelatedFiles()
    {
        return $this->hasMany(TaskRelatedFile::className(), ['task_id' => 'id']);
    }
}
