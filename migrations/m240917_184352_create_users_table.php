<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m240917_184352_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            "username" => $this->string(20)->notNull()->defaultValue(''),
            "password" => $this->string()->notNull()->defaultValue(''),
            "email" => $this->string()->notNull()->defaultValue(''),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            "nome" => $this->string()->notNull()->defaultValue(''),
        ]);

        $this->createIndex(
            'idx-user',
            'users',
            'username',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
