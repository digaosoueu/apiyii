<?php

namespace app\controllers;

use app\models\Livros;
use Yii;
use yii\data\Pagination;

class LivrosController extends \yii\rest\Controller
{  
    public function actionIndex($id = 0)
    {  
        $auth = new \app\models\Auth;
        $response = $auth->checkToken();

        try
        {
            if($response)
            {
                $request = Yii::$app->request;
                $model = json_decode(file_get_contents('php://input'), true);

                //Requisição tipo get /livros - Lista todos os livros | /livro/numero(id ou isbn) - lista o item especifico
                if($request->isGet)
                {  
                        $limit = 100;
                        $offset = 0;
                        $where = "";

                        if(isset($model["paginacao"]))
                        {
                            if(isset($model["paginacao"]["limit"]))
                            {
                                $limit = $model["paginacao"]["limit"];
                            }

                            if(isset($model["paginacao"]["offset"]))
                            {
                                $offset = $model["paginacao"]["offset"];
                            }
                        }
                        
                        if(isset($model["filter"]))
                        {
                            if(isset($model["filter"]["title"]))
                            {
                                if(!empty($model["filter"]["title"]))
                                { 
                                    $where = "title like '%" . $model["filter"]["title"] . "%'";
                                }
                            }
                            
                            if(isset($model["filter"]["isbn"]))
                            {
                                if(!empty($model["filter"]["isbn"])){
                                    
                                    if($where != "")
                                    {
                                            $where .= " or ";
                                    }
                                    
                                    $where .= " isbn like '%" . $model["filter"]["isbn"] . "%'";                              
                                }
                            }

                            if(isset($model["filter"]["authors"]))
                            {
                                if(!empty($model["filter"]["authors"])){
                                    
                                    if($where != "")
                                    {
                                            $where .= " or ";
                                    }
                                    
                                    $where .= " authors like '%" . $model["filter"]["authors"] . "%'";                              
                                }
                            }                   
                            
                        }   
                        
                        $orderBy = "";
                        //order by pode ser usado qualquer campo pra ordenação
                        if(isset($model["orderBy"]))
                        {
                            if(isset($model["orderBy"]["order"]))
                            {
                                if(!empty($model["orderBy"]["order"]))
                                {
                                    $orderBy = $model["orderBy"]["order"];
                                }
                            }

                            if(isset($model["orderBy"]["type"]))
                            {
                                if(!empty($model["orderBy"]["type"]))
                                {
                                    $orderBy .= " " . $model["orderBy"]["type"];
                                }
                                else
                                {
                                    $orderBy .= " asc ";
                                }
                            }
                        }
                        
                        $query = Livros::find();
                        
                        if($where != "")
                        {                         
                            $query = $query->andWhere
                            (
                                $where
                            );
                        }

                        //usado no get produto/numero(id ou isbn)
                        if($id > 0)
                        {
                            $query = $query->Where
                            (
                                ['or',
                                    ['id' => $id],
                                    ['isbn' => $id]
                                ]
                            );
                        }

                        if($orderBy != "")
                        {
                            $query = $query->orderBy
                            (
                                $orderBy
                            );
                        }

                        $livros = $query
                        ->offset($offset)
                        ->limit($limit)
                        ->all();

                        return $this->asJson($livros);
                }                
                else if($request->isPost || $request->isPut || $request->isDelete)
                {
                    //esse campo é o unico obrigatorio em todos os processos, sem ele não da pra continuar
                        if(!isset($model["isbn"]))
                        {  
                            $livro->addError("Erro: ", "isbn é obrigatorios");  
                            return $livro->errors;
                        }  

                        $campoEncontrado = false;

                        $livro = new Livros();  
                        
                        $isbn = $model["isbn"];
                        
                        if(!$request->isDelete){

                            //se esse campo for true o sistema vai dar prioridade aos dados do site brasilapi
                            if($model["isDadosSite"])
                            {
                                ///inser os dados do link https://brasilapi.com.br/docs#tag/ISBN/
                                if($isbn != "")
                                {
                                    $client = new \yii\httpclient\Client();
                                    $requests = [
                                        'isbn' => $client->get
                                        (
                                            'https://brasilapi.com.br/api/isbn/v1/' . $isbn
                                        )            
                                    ];

                                    $responses = $client->batchSend($requests);
                                    $retorno = $responses['isbn']->getContent();

                                    $retDecode = json_decode($retorno);
                                    
                                    ///caso não encontre o livro
                                    if(isset($retDecode->type))
                                    {
                                        if($retDecode->type == "bad_request")
                                        {      
                                            $livro->addError("Erro: ", $retDecode->message);  
                                            return $livro->errors;
                                        }                                    
                                    }
                                    else if(isset($retDecode->isbn))
                                    {
                                        ///preenche os campos com os dados do site
                                        foreach ($retDecode as $key => $val)
                                        { 
                                            if($key == "authors")
                                            {
                                                $val = json_encode($val);                
                                            }  

                                            if($key == "retail_price")
                                            {
                                                if($val != null)
                                                {
                                                    $key = "preco";
                                                }
                                            } 

                                            ///preenche campos do livro com os dados do site
                                            foreach($livro as $chaveLiv => $valorLiv)
                                            {
                                                if($chaveLiv == $key)
                                                {
                                                    $livro[$chaveLiv] = $val;  
                                                }
                                                
                                            }                                                                        
                                        }
                                        ////compara os campos do site com algum campo enviado, se o campo do site for nulo e no playload estiver preenchido o sistema preenche o campo
                                        foreach($model as $chave => $valor)
                                        {  
                                            if($chave == "estoque")
                                            {
                                                $livro["estoque"] = $valor;
                                            }   
                                            ///verifica se tem campo vazio no livro e preenchido com o do payload se vier
                                            foreach ($livro as $key => $val)
                                            {
                                                if($chave == $key)
                                                {
                                                    if(empty($val))
                                                    {
                                                        if(!empty($valor))
                                                        {
                                                            if($chave == "authors")
                                                            {
                                                                $valor = json_encode($valor);                
                                                            } 
                                                            $livro[$key] = $valor; 
                                                        }
                                                    }
                                                }
                                            }
                                        } 
                                    }                               
                                }
                            }
                            //caso queria o envio dos dados manualmente sem o uso do site
                            else
                            {
                                //verifica se no payload contem todos os campos pra cadastrar o livro
                                foreach ($livro as $chave => $valor) 
                                {   
                                    if($chave != "id")
                                    {
                                        $campoEncontrado = false;
                                        
                                        foreach ($model as $chaveM => $valorM)
                                        {                             
                                            if($chave == $chaveM)
                                            {
                                                    $campoEncontrado = true;

                                                    if($chaveM == "authors")
                                                    {
                                                        if($valorM != "")
                                                        {
                                                            $livro[$chave] = json_encode($valorM);
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $livro[$chave] = $valorM;
                                                    }                                        
                                            }                                   
                                        }
                                        if($request->isPost && !$campoEncontrado)
                                        { 
                                            $livro->addError("Erro: ", "Todos os campos são obrigatorios.");  
                                            return $livro->errors;  
                                        }  
                                    } 
                                }  
                            }

                        }                                                               

                        if($request->isPut || $request->isDelete)
                        {
                            $cli = Livros::find()
                            ->where(['isbn' => $isbn])
                            ->one();

                            if($cli)
                            {
                                $livro->id = $cli->id;
                            }                        
                        }
                        //valida os campos enviado ou do site
                        if($livro->validate())
                        {                         
                            if($request->isPut)
                            { 
                                if($cli)
                                {
                                    ///atualiza os dados com os novos dados do site ou payload
                                    foreach ($livro as $chave => $valor) 
                                    {
                                        if($valor <= 0 && $valor != "") 
                                        {
                                            $valor = "-0";
                                        }

                                        if(!empty($valor) && $chave != "isbn")
                                        {
                                            $cli[$chave] = $valor; 
                                        }
                                    }
                                    $cli->save(false);
                                    return $this->asJson($cli);
                                }
                            }
                            else if($request->isDelete)
                            {                                
                                if($cli)
                                {
                                    $cli->delete(false);
                                }
                                return $this->asJson($cli);
                            }
                            else
                            {    
                                $livro->insert();   
                                return $this->asJson($livro);                             
                            }
                        }
                        else
                        {                         
                            return $livro->errors;
                        }                        
                }               
                else
                {
                        throw new \yii\web\NotFoundHttpException;
                }
            }
            else
            {
                throw new \yii\web\UnauthorizedHttpException;
            }
        }
        catch (Exception $e) {

            throw new \yii\web\ServerErrorHttpException;
        }
    }     
}
