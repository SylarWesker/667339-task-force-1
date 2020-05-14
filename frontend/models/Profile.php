<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $avatar_filepath
 * @property string|null $address
 * @property string|null $birthday
 * @property string|null $about
 * @property string|null $phone
 * @property string|null $skype
 * @property string|null $another_messenger
 * @property int|null $view_count
 * @property string|null $last_activity_date
 * @property int|null $new_message_notification
 * @property int|null $new_response_notification
 * @property int|null $new_task_action_notification
 * @property int|null $show_contacts_only_to_client
 * @property int|null $hide_profile
 *
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'view_count', 'new_message_notification', 'new_response_notification', 'new_task_action_notification', 'show_contacts_only_to_client', 'hide_profile'], 'integer'],
            [['birthday', 'last_activity_date'], 'safe'],
            [['avatar_filepath', 'address', 'about', 'phone', 'skype', 'another_messenger'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'avatar_filepath' => 'Avatar Filepath',
            'address' => 'Address',
            'birthday' => 'Birthday',
            'about' => 'About',
            'phone' => 'Phone',
            'skype' => 'Skype',
            'another_messenger' => 'Another Messenger',
            'view_count' => 'View Count',
            'last_activity_date' => 'Last Activity Date',
            'new_message_notification' => 'New Message Notification',
            'new_response_notification' => 'New Response Notification',
            'new_task_action_notification' => 'New Task Action Notification',
            'show_contacts_only_to_client' => 'Show Contacts Only To Client',
            'hide_profile' => 'Hide Profile',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }
}
