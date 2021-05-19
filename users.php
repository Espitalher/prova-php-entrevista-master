<?php

//Requisição do arquivo que contém a classe com as funções de banco de dados
require_once "connection.php";

//Nova instância da classe Connection
$connection = new Connection();

//Formulario de cadastro de um novo usuário
echo "
    <b>Cadastrar usuários</b><hr>
    
    <form action='index.php' method='POST'>

        <label>Nome</label>
        <input type='text' name='nomeNovoUsuario' maxlength='100' required>

        <label>Email</label>
        <input type='email' name='emailNovoUsuario' maxlength='100' required>
        
        <button type='submit'> Cadastrar usuário</button>

    </form>";

//Verifica se o POST contém dados nas respetivas celulas para o insert ser feito no banco de dados
if(isset($_POST["nomeNovoUsuario"]) && isset($_POST["emailNovoUsuario"])){
    
    //Armazena os dados nas variáveis
    $nomeNovoUsuario    = $_POST["nomeNovoUsuario"];
    $emailNovoUsuario   = $_POST["emailNovoUsuario"];
    
    //Insert de um novo usuário no banco de dados SQLite    
    $connection->query("INSERT INTO users(name, email) VALUES ('$nomeNovoUsuario', '$emailNovoUsuario')");
    
    //Informa sucesso ao usuário
    echo "<p style='background-color:LightGreen;'>Usuário <b>".$nomeNovoUsuario."</b> inserido com sucesso!</p>";

}

//Verifica se o GET contém dados nas respetivas celulas para o delete ser feito no banco de dados
if(isset($_GET["id"]) && $_GET["action"] == "delete"){

    //Armazena os dados na variável
    $idUsuarioDeletado = $_GET["id"];

    //Confere se o conteúdo condiz com o campo id, evitando sql injection
    if(is_numeric($idUsuarioDeletado)){

        //Delete das cores vinculadas ao usuário
        $connection->query("DELETE FROM user_colors WHERE user_id = $idUsuarioDeletado");        
        
        //Delete do usuário solicitado
        $connection->query("DELETE FROM users WHERE id = $idUsuarioDeletado");

        //Informa sucesso ao usuário
        echo "<p style='background-color:Moccasin;'>Usuário id <b>".$idUsuarioDeletado."</b> excluído com sucesso!</p>";
    
    }else{

        echo "<p style='background-color:Salmon;'>Id de usuário <b>".$idUsuarioDeletado."</b> inválido!</p>";
    }

}

//Seleciona todos os usuários cadastrados no banco de dados e apresenta em uma tabela
$users = $connection->query("SELECT id, name, email FROM users");

echo "
    <b>Lista de usuários</b><hr>

    <table border='1'>

        <tr>
            <th>ID</th>    
            <th>Nome</th>    
            <th>Email</th>
            <th>Ações</th>    
        </tr>";

foreach($users as $user) {

    echo sprintf("
        <tr>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
            <td>
                <a href='index.php?action=edit&id=%s'>Editar</a>
                <a href='index.php?action=delete&id=%s'>Excluir</a>
            </td>
        </tr>",
        $user->id, $user->name, $user->email, $user->id, $user->id);

}

echo "</table>";

?>