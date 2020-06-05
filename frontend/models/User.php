<?php

namespace frontend\models;

use frontend\models\query\UserQuery;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property string|null $add_data
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $locality_id
 *
 * @property FavoritePerformer[] $favoritePerformers
 * @property Profile $profile
 * @property Task[] $tasksAsCreator
 * @property Task[] $tasksAsPerformer
 * @property Locality $locality
 * @property UserPortfolio[] $userPortfolios
 * @property UserSpecialization[] $userSpecializations
 */
class User extends \yii\db\ActiveRecord
{
    // ToDo!!!
    // Модель же раздует от дополнительных полей!!! Как с этим быть???
//     private $rating; // средний рейтинг
//     private $reviewsCount; // кол-во отзывов
     // public $tasksAsPerformerCount = 0; // кол-во заданий, которые выполняет в качестве исполнителя.

    public $rating;
    public $reviewsCount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password', 'full_name'], 'required'],
            [['add_data'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['locality_id'], 'integer'],
            [['email', 'password', 'full_name'], 'string', 'max' => 255],
            [['email'], 'unique'],
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
            'email' => 'Email',
            'password' => 'Password',
            'full_name' => 'Full Name',
            'add_data' => 'Add Data',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'locality_id' => 'Locality ID',
        ];
    }

    /**
     * Gets query for [[FavoritePerformers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavoritePerformers()
    {
        return $this->hasMany(FavoritePerformer::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery|TaskQuery
     */
    public function getTasksAsPerformer()
    {
        return $this->hasMany(Task::className(), ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery|TaskQuery
     */
    public function getTasksAsCreator()
    {
        return $this->hasMany(Task::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Locality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocality()
    {
        return $this->hasOne(Locality::className(), ['id' => 'locality_id']);
    }

    /**
     * Gets query for [[UserPortfolios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserPortfolios()
    {
        return $this->hasMany(UserPortfolio::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserSpecializations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpecializations()
    {
        return $this->hasMany(UserSpecialization::className(), ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Возвращает рейтинг пользователя (среднее арифмитическое от оценок за задания).
     *
     * @return float|null
     */
//    public function getRating()
//    {
//        if ($this->isNewRecord) {
//            return null; // this avoid calling a query searching for null primary keys
//        }
//
////        $rating = $this::find()->select(['rating' => 'AVG(review.rate)'])
////                        ->leftJoin('task','task.performer_id = user.id')
////                        ->where(['not', ['task.performer_id' => null]])
////                        ->leftJoin('review','review.task_id = task.id')
////                        ->groupBy('user.id')
////                        ->scalar();
//
//        if ($this->rating === null) {
//            $this->rating = $this->getTasksAsPerformer()
//                ->leftJoin('review','review.task_id = task.id')
//                ->average('review.rate');
//        }
//
//        return $this->rating;
//    }

    /**
     * Количество отзывов.
     *
     * @return int|null
     */
//    public function getReviewsCount()
//    {
//        if ($this->isNewRecord) {
//            return null; // this avoid calling a query searching for null primary keys
//        }
//
//        // $reviewsCount = $this->select(['reviewCount' => 'COUNT(review.id)'])
//        //        $reviewsCount = $this::find()
//        //            ->leftJoin('task','task.performer_id = user.id')
//        //            ->where(['not', ['task.performer_id' => null]])
//        //            ->leftJoin('review','review.task_id = task.id')
//        //            ->count('review.id');
//
//        if ($this->reviewsCount === null) {
//            $this->reviewsCount = $this->getTasksAsPerformer()
//                ->leftJoin('review', 'review.task_id = task.id')
//                ->count('review.id');
//        }
//
//        return $this->reviewsCount;
//    }
}
