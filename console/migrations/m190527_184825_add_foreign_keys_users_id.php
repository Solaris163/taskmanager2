<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m190527_184825_add_foreign_keys_users_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('fk_creators', 'tasks', 'creator_id', 'user', 'id');
        $this->addForeignKey('fk_responsible', 'tasks', 'responsible_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
