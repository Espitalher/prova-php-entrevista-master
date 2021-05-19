<?php

//Requisição do arquivo que contém a classe com as funções de banco de dados
require_once "connection.php";

//Nova instância da classe Connection
$connection = new Connection();

//Formulario de cadastro de uma nova cor
echo "
    <b>Cadastrar cores</b><hr>
    
    <form action='index.php?action=colors' method='POST'>

        <label>Nome da cor</label>
        <input type='text' name='nomeNovaCor' maxlength='50' required>

        <label>Hex Code RGB</label>
        <input type='color' name='hexRgbNovaCor' required>
        
        <button type='submit'> Cadastrar cor</button>

    </form>";


//Verifica se o POST contém dados nas respetivas celulas para o insert ser feito no banco de dados
if(isset($_POST["nomeNovaCor"]) && isset($_POST["hexRgbNovaCor"])){

    //Armazena os dados nas variáveis
    $nomeNovaCor    = $_POST["nomeNovaCor"];
    $hexRgbNovaCor  = $_POST["hexRgbNovaCor"];

    //Verifica se já existe algum registro de cor com mesmo nome ou hex code
    $testeCores         = $connection->query("SELECT id FROM colors WHERE name = '$nomeNovaCor' OR hexRgb = upper('$hexRgbNovaCor')");
    $contadorTesteCores = $testeCores->fetchColumn();

    //Caso o nome da cor e o Hex Code não estejam cadastrados
    if($contadorTesteCores === 0 || !isset($contadorTesteCores) || $contadorTesteCores == ""){
        
        //Insert de uma nova cor no banco de dados SQLite    
        $connection->query("INSERT INTO colors(name, hexRgb) VALUES ('$nomeNovaCor', upper('$hexRgbNovaCor'))");
                
        //Informa sucesso ao usuário
        echo "<p style='background-color:$hexRgbNovaCor;'>Cor <b>".$nomeNovaCor."</b> cadastrada com sucesso!</p>";
    
    }elseif($contadorTesteCores > 0){

        //Informa erro ao usuário
        echo "<p style='background-color:Salmon;'>Cor <b>".$nomeNovaCor."</b> ou Hex Code ".$hexRgbNovaCor." já cadastrado(s)!</p>";

    }

}


//Verifica se o GET contém dados nas respetivas celulas para o delete ser feito no banco de dados
if(isset($_GET["id"]) && $_GET["action"] == "deleteColor"){

    //Armazena os dados na variável
    $idCorDeletada = $_GET["id"];

    //Confere se o conteúdo condiz com o campo id, evitando sql injection
    if(is_numeric($idCorDeletada)){

        //Delete da cor vinculada aos usuários
        $connection->query("DELETE FROM user_colors WHERE color_id = $idCorDeletada");        
        
        //Delete da cor solicitada
        $connection->query("DELETE FROM colors WHERE id = $idCorDeletada");

        //Informa sucesso ao usuário
        echo "<p style='background-color:Moccasin;'>Cor id <b>".$idCorDeletada."</b> excluída com sucesso!</p>";
    
    }else{

        echo "<p style='background-color:Salmon;'>Id de cor <b>".$idCorDeletada."</b> inválida!</p>";
    }

}


//Seleciona todos os usuários cadastrados no banco de dados e apresenta em uma tabela
$colors = $connection->query("SELECT id, name, hexRgb FROM colors");

echo "
    <b>Lista de cores</b><hr>

    <table border='1'>

        <tr>
            <th>ID</th>    
            <th>Nome</th>    
            <th>Hex Code</th>
            <th>Ação</th>    
        </tr>";

foreach($colors as $color) {

    echo sprintf("
        <tr>
            <td>%s</td>
            <td>%s</td>
            <td style='background-color:%s;'>%s</td>
            <td>                
                <a href='index.php?action=deleteColor&id=%s'>Excluir</a>
            </td>
        </tr>",
        $color->id, $color->name, $color->hexRgb, $color->hexRgb, $color->id);

}

echo "</table>";




?>