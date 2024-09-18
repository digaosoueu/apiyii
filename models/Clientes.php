<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string $nome
 * @property string $cpf
 * @property string $sexo
 */
class Clientes extends \yii\db\ActiveRecord
{
    public $validaCampos = true;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $nome = $this->nome;
        $cpf = $this->cpf;
        $email = $this->email;
        $sexo = $this->sexo;
        $endereco = $this->endereco;
        $numero = $this->numero;
        $bairro = $this->bairro;
        $cep = $this->cep;
        $cidade = $this->cidade;
        $estado = $this->estado;

        return [
            [['nome', 'sexo', 'endereco', 'numero', 'bairro'], 'validaCamposVazios'],
            [['nome', 'cpf'], 'string', 'max' => 255],
            ['sexo', 'string', 'max' => 1],
            ['cpf', 'string', 'min' => 11],
            ['cpf', 'validaCpf'],
            ['cpf', 'validaCpfUnico'],
            ['estado', 'string', 'max' => 2],
            ['email', 'email', 'message' => 'email invalido.'],
            ['cep', 'validaCEP']      
        ];
    }

    function validaCamposVazios()
    {
        if(!$this->validaCampos)
        {
            if(empty($this->cpf))
            {
                $this->addError('cpf', 'cpf é obrigatário');
            }
        }
        else
        {        
            $insereErro = false;    
            foreach ($this->attributes as $chave => $valor) 
            {       
                $insereErro = true;

                if($chave == "id" || $chave == "complemento" || !empty($valor))
                {
                    $insereErro = false;
                }

                foreach ($this->errors as $chaveError => $valorError) 
                {                    
                    if(empty($valor))
                    {
                        if($chave == "id" || $chave == $chaveError ||$chave == "complemento")
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

    function validaCPF() 
    {
        $cpf = $this->cpf;
        $response = true;

        // deixa somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
        
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            $response = false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $response = false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) 
        {
            for ($d = 0, $c = 0; $c < $t; $c++) 
            {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) 
            {
                $response = false;
                break;
            }
        }                
        
        if(!$response){
            $this->addError('cpf', 'cpf invalido');
        }    
    }

    public function validaCpfUnico()
    {
        $cliente = Clientes::find()
        ->where(['cpf' => $this->cpf])
        ->one();

        if($cliente && $this->validaCampos)
        {
            $this->addError('cpf', 'cpf ja cadastrado');
        }
    }

    public function validaCEP()
    {
        $client = new \yii\httpclient\Client();
        $requests = [
            'cep' => $client->get('https://brasilapi.com.br/api/cep/v1/' . $this->cep)            
        ];

        $responses = $client->batchSend($requests);
        $cepretorno = $responses['cep']->getContent();

        $cepDecode = json_decode($cepretorno);
        
        if (empty($cepDecode->cep))
        {
            $this->addError('cep', 'cep invalido');
        }      
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'cpf' => 'Cpf',
            'sexo' => 'Sexo',
            'email' => 'email',
            'endereco' => 'endereco',
            'numero' => 'numero',
            'bairro' => 'bairro',
            'cep' => 'cep',
            'cidade' => 'cidade',
            'estado' => 'estado'
        ];
    }    
}
