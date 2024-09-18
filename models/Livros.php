<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "livros".
 *
 * @property int $id
 * @property string $isbn
 * @property string $title
 * @property string|null $authors
 * @property float $preco
 * @property int $estoque
 */
class Livros extends \yii\db\ActiveRecord
{
    public function attributes()
    {
        return ['isbn', 'title', 'authors', 'preco', 'estoque', 'id'];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'livros';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $isbn = $this->isbn;
        $title = $this->title;
        $authors = $this->authors;
        $preco = $this->preco;
        $estoque = $this->estoque;

        return [
            [['estoque'], 'integer'],
            [['title', 'preco'], 'validaCamposVazios'],
            [['preco'], 'number'],            
            [['isbn'], 'string', 'max' => 20],
            ['isbn', 'validaCadastroUnico'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    function validaCamposVazios()
    {
        
        if($this->id > 0)
        {
            if(empty($this->isbn))
            {
                $this->addError('isbn', 'isbn é obrigatário');
            }
        }
        else
        {        
            $insereErro = false;    
            foreach ($this->attributes as $chave => $valor) 
            {       
                $insereErro = true;
                $valor = settype($valor, "string");

                if($chave == "id" || !empty($valor))
                {
                    $insereErro = false;
                }

                foreach ($this->errors as $chaveError => $valorError) 
                {                    
                    if(empty($valor))
                    {
                        echo $chave . "=" . $valor ."<br>";
                        if($chave == "id" || $chave == $chaveError)
                        {                           
                            $insereErro = false; 
                            break;
                        }                        
                    }                     
                }

                if($insereErro)
                {                                 
                    $this->addError($chave, "esse campo é obrigatário");                    
                }
            }                   
        }
        return true;
    }

    public function validaCadastroUnico()
    {
        $livro = Livros::find()
        ->where(['isbn' => $this->isbn])
        ->one();

        if($livro && $this->id == 0)
        {
            $this->addError('isbn', 'isbn ja cadastrado');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'isbn' => 'Isbn',
            'title' => 'Title',
            'authors' => 'Authors',
            'preco' => 'Preco',
            'estoque' => 'Estoque'
        ];
    }
}
