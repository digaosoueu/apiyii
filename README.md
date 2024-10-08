<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Desenvolvimento API em Yii2 Framework</h1>
    <br>
</p>

------------

PHP Version 8.3.11
mysqlnd 8.3.11


Instalação
------------
pra iniciar a aplicação

Em um terminal, siga até a pasta que esta sua instalação e digite:

php yii serve

caso queira escolher a porta:

php yii serve --port=8000

http://localhost:8000

Configuração
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=apiyii2',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];

=================

1) Criar as tabelas necessarias
 - Execute o camando "yii migrate" esse comando vai criar as tabelas

2) Criar um usuario pra ter acesso :
 - Execute o arqui "inserir_usuario.sh" e siga as instruções

Autenticação

Post:
/auth/login

Body:
{
    "username": "username",
    "password": "senha"
}

vai retornar um token, esse token precisa ser enviado no hearder das requisições pra ser autorizado.

Authorization: Bearer token


Clientes:
=====================================

 Inserir:

POST
/clientes

body:
{    
    "nome": "",
    "cpf": "",
    "sexo": "",
    "email": "",
    "endereco": "",
    "numero": "",
    "bairro": "",
    "cep": "",
    "cidade": "",
    "estado": "",
    "complemento": ""
}
- Todos os campos são obrigatórios menos "complemento"

Lista
GET
/cliente/1

Lista esse cliente especificamente.

GET
/clientes

Lista todos os clientes

Com essa estrutura você pode filtrar order e fazer paginação.
A ordenação pode ser feita com qualquer campo o filtro apenas com nome e cpf
{
    "paginacao":
    {
        "limit": 10,
        "offset":0
    },
    "filter": {
        "nome": "",
        "cpf": ""
    },
    "orderBy": {
        "order": "bairro",
        "type": "desc"
    }
}

 Editar
 PUT
 /clientes

 body:
{    
    "nome": "",
    "cpf": "",
    "sexo": "",
    "email": "",
    "endereco": "",
    "numero": "",
    "bairro": "",
    "cep": "",
    "cidade": "",
    "estado": "",
    "complemento": ""
}
- Pode enditar qualquer campo menos cpf, o cpf é obrigatorio, caso envie um campo vazio o mesmo não será alterado

 Deletar

 DELETE
 /clientes

 body:
{   
    "cpf": ""
}
 - Apenas o cpf é obrigatório


 Livros
 =========================

 Inserir:

POST
/livros

body:
{    
    "isbn": "12345",
    "title": "teste",
    "authors": [
        "teste 1",
        "teste 2",
        "teste 3"
    ],
    "preco": 10.00,
    "estoque": 5,
    "isDadosSite": true

}

Tem a opção de inserir um livro "manualmente" ou direto do site "https://brasilapi.com.br/api/isbn/v1/"
Enviando "isDadosSite" = true o sistema vai inserir os dados do site de acordo com numero do "isbn", caso seja um valor válido, caso algum dado seja retornado nulo e o mesmo seja preenchido na api o sistema vai considerar o preenchimento manual, se o campo retornar da api o mesmo será igmorado
Se a "isDadosSite" for false todo o preechimento será manual

Lista
GET
/livros/1 ou isbn

Lista esse livro especificamente.

GET
/livros

Lista todos os livros

Com essa estrutura você pode filtrar order e fazer apginação.
A ordenação pode ser feita com qualquer campo o filtro apenas com title e isbn e autor
{
    "paginacao":
    {
        "limit": 10,
        "offset":0
    },
    "filter": {
        "title": "",
        "isbn": "",
	    "authors": ""

    },
    "orderBy": {
        "order": "title",
        "type": "desc"
    }
}

 Editar
 PUT
 /livros

 body:
{    
    "isbn": "12345",
    "title": "teste2",
    "authors": [
        "teste 1",
        "teste 2",
        "teste 3"
    ],
    "preco": 10.00,
    "estoque": 5,
    "isDadosSite": true
}
- A edição segue a mesma regra da inserção, o campo "isbn" é obrigatorio e não editavel

 Deletar

 DELETE
 /livros

 body:
{   
    "isbn": ""
}
 - Apenas o isbn é obrigatório
