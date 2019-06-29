<?php

namespace common\models\tables;

use app\VarDump;
use common\models\User;
use Imagine\Gd\Image;
use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property int $task_id
 *
 * @property Tasks $task
 * @property Users $user
 */
class Files extends \yii\db\ActiveRecord
{
    public $upload; //добавил свойство $upload для загрузки файлов

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['user_id', 'task_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['upload', 'file', 'extensions' => 'jpg, png'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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

    public function saveFile()
    {
        $filepath = \Yii::getAlias("@webroot/img/{$this->upload->name}"); //находим адрес для сохранения файла
        $this->upload->saveAs($filepath); //сохраняем файл

        \yii\imagine\Image::thumbnail($filepath, 80, 80) //сохраняем превью файла через расширение imagine
            ->save(\Yii::getAlias("@webroot/img/small/$this->upload"));
    }

    //метод возвращает картинки, загруженные для задачи с данным id
    public static function getPictures($task_id)
    {
        return static::find()->select(['name'])->where(['task_id' => $task_id])->asArray()->all();
    }
}
