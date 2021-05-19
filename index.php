<?php

//
// Sistema: CRUD usuários
// Autor: Gabriel Espitalher
// Criação: 18/05/2021
//

//Cabeçalho
echo "
<a href='index.php'><button>Home</button></a>

<a href='index.php?action=colors'><button>Cadastro de cores</button></a>

<br><br>";

//Página inicial do sistema
if(!isset($_GET["action"]) || $_GET["action"] == "delete"){

    //Cadastro, listagem e exclusão de usuários
    require_once "users.php";

}elseif($_GET["action"] == "colors" || $_GET["action"] == "deleteColor"){

    //Cadastro de cores
    require_once "colors.php";    

}elseif($_GET["action"] == "edit" || $_GET["action"] == "deleteColorUser"){

    //Edição de usuários e vinculação de cores
    require_once "usersEdition.php";

}else{
    
    //Retorna para página inicial caso o GET['action'] não se enquadre nas condições
    header("Location: index.php");

}

?>