<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notes}}`.
 */
class create_notes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notes}}', [
            'id' => $this->primaryKey(),
            'text' => $this->char(255),
            'level' => $this->integer(),
            'top' => $this->integer(),
            'left' => $this->integer(),
            'user_id' => $this->integer(),
        ]);

        $this->insert('notes', [
            'text' => 'Новая заметка',
            'level' => 1,
            'top' => 300,
            'left' => 300,
            'user_id' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notes}}');
    }
}
