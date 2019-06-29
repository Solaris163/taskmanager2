<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m190616_101450_create_files_table extends Migration
{
    protected $tableName = 'files';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'user_id' => $this->integer(),
            'task_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_tasks_id', $this->tableName, 'task_id', 'tasks', 'id');
        $this->addForeignKey('fk_users_id', $this->tableName, 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%files}}');
    }
}
