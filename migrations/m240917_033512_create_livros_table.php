<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%livros}}`.
 */
class m240917_033512_create_livros_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%livros}}', [
            'id' => $this->primaryKey(),
            "isbn" => $this->string(20)->notNull()->defaultValue(''),
            "title" => $this->string()->notNull()->defaultValue(''),
            "authors" => $this->text(),
            "preco" => $this->decimal(8, 2)->notNull()->defaultValue(0),
            "estoque" => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'idx-isbn',
            'livros',
            'isbn',
            true
        );

        $this->createIndex(
            'idx-title',
            'livros',
            'title'
        );

        $this->createIndex(
            'idx-authors',
            'livros',
            'authors'
        );
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%livros}}');
    }
}
