<?php

namespace app\controllers;

use app\models\Clientes;
use Yii;
use yii\data\Pagination;

class ClientesController extends \yii\rest\Controller
{  
     public function actionIndex($id = 0)
     {  
          $auth = new \app\models\Auth;
          $response = $auth->checkToken();
          
          if($response)
          {               
               $request = Yii::$app->request;
               $model = json_decode(file_get_contents('php://input'), true);

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
                         if(isset($model["filter"]["nome"]))
                         {
                              if(!empty($model["filter"]["nome"]))
                              { 
                                   $where = "nome like '%" . $model["filter"]["nome"] . "%'";
                              }
                         }
                         
                         if(isset($model["filter"]["cpf"]))
                         {
                              if(!empty($model["filter"]["cpf"])){

                                   $filterCpf = (int)filter_var($model["filter"]["cpf"], FILTER_SANITIZE_NUMBER_INT);

                                   if($where != "")
                                   {
                                        $where .= " or ";
                                   }
                                   
                                   $where .= " cpf like '%" . $filterCpf . "%'";                              
                              }
                         }  
                    }   
                    
                    $orderBy = "";

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

                    $query = Clientes::find();
                    $count = $query->count();
                    
                    if($where != "")
                    {                         
                         $query = $query->andWhere
                         (
                              $where
                         );
                    }

                    if($id > 0){
                         $query = $query->Where
                         (
                              ['id' => $id]
                         );
                    }

                    if($orderBy != "")
                    {
                         $query = $query->orderBy
                         (
                              $orderBy
                         );
                    }

                    $clientes = $query
                    ->offset($offset)
                    ->limit($limit)
                    ->all();

                    return $this->asJson($clientes);
               }
               else if($request->isPost || $request->isPut || $request->isDelete)
               {
                    //Esse campo é obrigatorio em todo o processo
                    
                    if(!isset($model["cpf"]))
                    {  
                         $cliente->addError("Erro: ", "CPF é obrigatorios");  
                         return $livro->cliente; 
                    }   
                    else
                    {
                         $cpf = str_replace(".", "", $model["cpf"]);
                         $cpf = str_replace("-", "", $cpf);
                    }                 

                    $campoEncontrado = false;
                    
                    $cliente = new Clientes();
                    
                    foreach ($cliente as $chave => $valor) 
                    {                            
                         if($chave != "id")
                         {
                              $campoEncontrado = false;
                              foreach ($model as $chaveM => $valorM)
                              {                             
                                   if($chave == $chaveM)
                                   {
                                        $campoEncontrado = true;
                                        if($chave == "cpf")
                                        {
                                             $cliente[$chave] = $cpf;
                                        }
                                        else
                                        {
                                             $cliente[$chave] = $valorM;
                                        } 
                                   }
                              }
                              if($request->isPost && !$campoEncontrado)
                              {    
                                   $cliente->addError("Erro: ", "Todos os campos são obrigatorios");  
                                   return $cliente->errors;                       
                              }  
                         } 
                    } 
                    
                    $cliente->validaCampos = true;

                    if($request->isPut || $request->isDelete)
                    {
                         $cliente->validaCampos = false;
                    }
                    
                    if($cliente->validate())
                    {                         
                         if($request->isPut)
                         {
                              $cli = Clientes::find()
                              ->where(['cpf' => $cliente->cpf])
                              ->one();

                              if($cli)
                              { 
                                   foreach ($model as $chave => $valor) 
                                   {
                                        $cli->validaCampos = false;

                                        if(!empty($valor) && $chave != "cpf" && $chave != "id")
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
                              $cli = Clientes::find()
                              ->where(['cpf' => $cliente->cpf])
                              ->one();
                              
                              if($cli)
                              {
                                   $cli->delete(false);
                              }
                              return $this->asJson($cli);
                         }
                         else
                         {
                              $cliente->insert();
                              return $this->asJson($cliente);
                         }
                    }
                    else
                    {                         
                         return $cliente->errors;
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
}