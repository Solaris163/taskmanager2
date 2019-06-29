<?php

namespace common\models\tables;

use app\VarDump;
use Yii;
use common\models\User;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property string $content
 * @property int $user_id
 * @property int $task_id
 *
 * @property Tasks $task
 * @property User $user
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'task_id'], 'integer'],
            [['content'], 'string', 'max' => 255],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
            'content' => 'Content',
            'user_id' => 'User ID',
            'task_id' => 'Task ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * метод возвращает массив из комментариев и имена их авторов по id задачи
     * @param int $task_id
     * @return array
     */
    public static function getComments($task_id)
    //Можно без join просто получить таблицу и связь к ней через метод with() как в commands\CheckDeadlinesController
    {
        return static::find()->select(['comments.content', 'user.username'])->where(['task_id' => $task_id])
            ->leftJoin('user', 'user.id = comments.user_id')
            ->asArray()
            ->all();
    }
}
