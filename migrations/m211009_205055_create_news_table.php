<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m211009_205055_create_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(40)->notNull(),
            'path' => $this->string(100)->notNull(),
            'type' => $this->string(10)->notNull(),
            'size' => $this->integer(11)->notNull(),
            'parent' => $this->integer(10),
        ]);

        $this->insert('files', [
            'name' => 'Хранилище',
            'path' => '',
            'type' => 'dir',
            'size' => '1000', 
            'parent' => 0 
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%news}}');
    }
}
