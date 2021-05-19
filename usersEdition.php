<?php

//Requisição do arquivo que contém a classe com as funções de banco de dados
require_once "connection.php";

//Nova instância da classe Connection
$connection = new Connection();

//Inicializa var de mensagem na tela
$msgUsuario = "";

//Verifica se o POST contém dados nas respetivas celulas para o update e insert de cores ser feito no banco de dados
if(isset($_POST["nomeUsuario"]) && isset($_POST["emailUsuario"])){
    
    //Captura id do GET
    $idUsuario = $_GET["id"];

    //Confere se o conteúdo condiz com o campo id, evitando sql injection
    if(is_numeric($idUsuario)){
    
    //Armazena os dados nas variáveis
    $nomeUsuario            = $_POST["nomeUsuario"];
    $emailUsuario           = $_POST["emailUsuario"];
    if(isset($_POST["colorsUserSelect"])){
        $colorsUserSelect       = $_POST["colorsUserSelect"];
    }    
    
    //Update de um usuário no banco de dados SQLite    
    $connection->query("UPDATE users SET name = '$nomeUsuario', email = '$emailUsuario' WHERE id = $idUsuario");
    
    if(isset($_POST["colorsUserSelect"])){
        foreach($colorsUserSelect AS $colorSelect){

            $connection->query("INSERT INTO user_colors (user_id, color_id) VALUES ($idUsuario, $colorSelect)");                

        }
    }

    //Informa sucesso ao usuário
    $msgUsuario = "<p style='background-color:LightGreen;'>Usuário <b>".$nomeUsuario."</b> atualizado com sucesso!</p>";

    }else{

        echo "<p style='background-color:Salmon;'>Id de usuário <b>".$idUsuario."</b> inválido!</p>";
    }

}

//Delete de cores do perfil do usuário
if(isset($_GET["action"]) && $_GET["action"] == "deleteColorUser"){
    
    //Captura id do GET
    $idUsuario = $_GET["id"];

    //Confere se o conteúdo condiz com o campo id, evitando sql injection
    if(is_numeric($idUsuario)){
    
    $deleteColor = $_GET["idColor"];

    //Delete da cor selecionada
    $connection->query("DELETE FROM user_colors WHERE user_id = $idUsuario AND color_id = $deleteColor");
    
    }else{

        echo "<p style='background-color:Salmon;'>Id de usuário <b>".$idUsuario."</b> inválido!</p>";
    }

}

//Verifica se o GET contém dados nas respetivas celulas para pesquisar o usuário
if(isset($_GET["action"]) && isset($_GET["id"])){

    //Captura id do GET
    $idUsuario = $_GET["id"];
    
    //Confere se o conteúdo condiz com o campo id, evitando sql injection
    if(is_numeric($idUsuario)){
        
        //Busca no banco de dados o cadastro do usuário em questão
        $userEdit = $connection->query("SELECT id, name, email FROM users WHERE id = $idUsuario");

        //Armazena os dados do usuário
        foreach($userEdit as $userRow){
            
            $nomeUsuario    = $userRow->name;
            $emailUsuario   = $userRow->email;
        }

        //Busca no banco de dados o cadastro de cores
        $colorsDisponiveis = $connection->query("SELECT id, name, hexRgb FROM colors WHERE id NOT IN (SELECT color_id FROM user_colors WHERE user_id = $idUsuario) ORDER BY name ASC");
        

    }else{

        echo "<p style='background-color:Salmon;'>Id de usuário <b>".$idUsuario."</b> inválido!</p>";
    }

}

//Formulario de edição de cadastro
echo "
    <b>Edição de usuário</b><hr>
    
    <form action='index.php?action=edit&id=$idUsuario' method='POST'>

        <label>Nome</label>
        <input type='text' name='nomeUsuario' value='".$nomeUsuario."' maxlength='100' required>

        <label>Email</label>
        <input type='email' name='emailUsuario' value='".$emailUsuario."' maxlength='100' required>
        
        <label>Cores</label>
        <select name='colorsUserSelect[]' multiple>";
            
            foreach($colorsDisponiveis as $color){

                $idCor      = $color->id;
                $hexRgbCor  = $color->hexRgb;
                $nameCor    = $color->name;

                echo "<option value='$idCor' style='background-color:$hexRgbCor;'>$nameCor</option>";
            }            
        
        echo
        "</select>
        
        <button type='submit'> Salvar edição</button>

    </form>";

    echo $msgUsuario;


    //Confere se o conteúdo condiz com o campo id, evitando sql injection
    if(is_numeric($idUsuario)){
        //Seleciona todas as cores cadastradas nesse usuário
        $colorsUser = $connection->query("SELECT C.id, C.name, C.hexRgb FROM user_colors AS UC LEFT JOIN colors AS C ON UC.color_id = C.id WHERE user_id = $idUsuario");
    }
echo "
    <b>Lista de cores cadastradas no usuário</b><hr>

    <table border='1'>

        <tr>
            <th>ID</th>    
            <th>Nome</th>    
            <th>Hex Code</th>
            <th>Ação</th>    
        </tr>";

foreach($colorsUser as $colorUser) {

    echo sprintf("
        <tr>
            <td>%s</td>
            <td>%s</td>
            <td style='background-color:%s;'>%s</td>
            <td>                
                <a href='index.php?action=deleteColorUser&id=%s&idColor=%s'>Excluir</a>
            </td>
        </tr>",
        $colorUser->id, $colorUser->name, $colorUser->hexRgb, $colorUser->hexRgb, $idUsuario, $colorUser->id);

}

echo "</table>";


?>