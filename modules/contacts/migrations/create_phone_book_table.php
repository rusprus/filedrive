<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%phone_book}}`.
 */
class create_phone_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%phone_book}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->char(255),
            'last_name' => $this->char(255),
            'add_names' => $this->char(255),
            'tel' => $this->char(255),
            'user_id' => $this->integer(10),
        ]);

        $this->insert('phone_book', [
            'first_name' => "Марков",
            'last_name' => "Захаров",
            'add_names' => "Философ",
            'tel' => 89213932342,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%phone_book}}');
    }
}
