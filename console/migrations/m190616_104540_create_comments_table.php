<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comments}}`.
 */
class m190616_104540_create_comments_table extends Migration
{
    protected $tableName = 'comments';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('comments', [
            'id' => $this->primaryKey(),
            'content' => $this->string(),
            'user_id' => $this->integer(),
            'task_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_tasks_id_for_comments', $this->tableName, 'task_id', 'tasks', 'id');
        $this->addForeignKey('fk_users_id_for_comments', $this->tableName, 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%comments}}');
    }
}
