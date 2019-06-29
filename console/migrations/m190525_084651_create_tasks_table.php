<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tasks}}`.
 */
class m190525_084651_create_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tasks', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment("Название задачи"),
            'description' => $this->string(),
            'creator_id' => $this->integer(),
            'responsible_id' => $this->integer(),
            'deadline' => $this->date(),
            'status_id' => $this->integer(),
            'create_time' => $this-> date(),
            'update_time' => $this->date(),
        ]);

        $this->createIndex("tasks_creator_idx", 'tasks', ['creator_id']);
        $this->createIndex("tasks_responsible_idx", 'tasks', ['responsible_id']);
   }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tasks');
    }
}
