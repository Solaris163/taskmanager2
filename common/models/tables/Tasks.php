<?php

namespace common\models\tables;

use app\VarDump;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $name Название задачи
 * @property string $description
 * @property int $creator_id
 * @property int $responsible_id
 * @property string $deadline
 * @property int $status_id
 *
 * @property $status
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['creator_id', 'responsible_id', 'status_id'], 'integer'],
            [['deadline'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            ////подставил вместо лейблов локализацию (было 'Name' подставил \Yii::t("app", "task_name")
            'id' => 'ID',
            'name' => \Yii::t("app", "task_name"),
            'description' => \Yii::t("app", "task_description"),
            'creator_id' => 'Creator ID',
            'responsible_id' => \Yii::t("app", "task_responsible"),
            'deadline' => \Yii::t("app", "deadline"),
            'status_id' => \Yii::t("app", "task_status"),
        ];
    }

    public function getStatus()
    {
        return $this->hasOne(TaskStatuses::class, ['id' => 'status_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    public function getResponsible()
    {
        return $this->hasOne(User::class, ['id' => 'responsible_id']);
    }

    //Добавлю связи для получения комментариев и изображений для задачи
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['task_id' => 'id']);
    } //Здесь task_id - из таблицы Comments, id - из таблицы Tasks

    public function getFiles()
    {
        return $this->hasMany(Files::class, ['task_id' => 'id']);
    } //Здесь task_id - из таблицы Files, id - из таблицы Tasks

    //метод возвращает массив, каждым элементом которого является
    //массив из id задачи, у которой остается сутки или меньше до срока выполнения, и email исполнителя этой задачи
    //можно делать запрос так как ниже: (см commands\CheckDeadlinesController)
    //$tasks = Tasks::find()->where("DATEDIFF(NOW(), tasks.deadline) <= 1")->with('responsible')->all();
    public static function getExpiresTasks()
    //Можно без join просто получить таблицу и связь к ней через метод with() как в commands\CheckDeadlinesController
    {
        return Yii::$app->db->createCommand("SELECT tasks.id, users.email FROM tasks
             LEFT JOIN user ON tasks.responsible_id = user.id
             WHERE DATEDIFF(tasks.deadline, NOW()) <= 1")
            ->queryAll();
    }

    // Подключаю поведение для записи даты создания и даты изменения строки в таблице tasks
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

}
