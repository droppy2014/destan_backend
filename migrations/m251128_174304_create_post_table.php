<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m251128_174304_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
   public function safeUp()
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->createIndex(
            'idx_post_created_at',
            '{{%post}}',
            'created_at'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%post}}');
    }
}
