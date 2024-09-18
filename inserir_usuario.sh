#!/bin/bash

# Configurações do banco de dados
HOST="127.0.0.1"
USER="digaosoueu"
PASSWORD="rodrigo123"
DATABASE="api"

# Solicitar informações ao usuário
echo "Digite o nome de usuário:"
read NOME_USUARIO

echo "Digite a senha do usuário:"
read -s SENHA_USUARIO  # -s para ocultar a senha enquanto é digitada

echo "Digite seu nome:"
read NOME

# Comando SQL para inserir o novo usuário
SQL="INSERT INTO users (nome, username, password) VALUES ('$NOME', '$NOME_USUARIO', SHA2('$SENHA_USUARIO', 256));"

# Conectar ao banco de dados e executar o comando SQL
mysql -h "$HOST" -u "$USER" -p"$PASSWORD" "$DATABASE" -e "$SQL"

# Verifica se o comando foi bem-sucedido
if [ $? -eq 0 ]; then
    echo "Usuário inserido com sucesso."
else
    echo "Erro ao inserir usuário."
fi
