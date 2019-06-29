<?php

namespace common\models\tables;

use Yii;

/**
 * This is the model class for table "task_statuses".
 *
 * @property int $id
 * @property string $name
 *
 * @property Tasks[] $tasks
 */
class TaskStatuses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::className(), ['status_id' => 'id']);
    }

    public  static function getTasksList() { //так делал преподаватель
        return static::find()->select(['name'])->indexBy('id')->column();
    }
}
