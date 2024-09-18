<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clientes}}`.
 */
class m240915_155757_create_clientes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%clientes}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string()->notNull()->defaultValue(''),
            'cpf' => $this->string()->notNull()->defaultValue(''),
            'sexo' => $this->string(2)->notNull()->defaultValue(''),
            'email' => $this->string()->notNull()->defaultValue(''),
            'endereco' => $this->string()->notNull()->defaultValue(''),
            'numero' => $this->string(10)->notNull()->defaultValue(''),
            'bairro' => $this->string()->notNull()->defaultValue(''),
            'cep' => $this->string(10)->notNull()->defaultValue(''),
            'cidade' => $this->string()->notNull()->defaultValue(''),
            'estado' => $this->string(2)->notNull()->defaultValue(''),
            'complemento' => $this->string()->notNull()->defaultValue('')
        ]);

        $this->createIndex(
            'idx-cpf',
            'clientes',
            'cpf',
            true
        );

        $this->createIndex(
            'idx-nome',
            'clientes',
            'nome'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%clientes}}');
    }
}
