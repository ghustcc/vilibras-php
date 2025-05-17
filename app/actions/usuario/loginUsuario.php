<?php
session_start();

require_once("../../config/conecta.php");

$msg;

# verifica de as variaveis foram definidas ou se estão vazias
if(empty($_POST['email'])){
    $msg = "Preencha o email";
}
elseif(empty($_POST['senha'])){
    $msg = "Preencha a senha";
}else{

    $emailForm = trim($_POST['email']);
    $senhaForm = trim($_POST['senha']);

    conecta();

    // fazendo a busca no banco para coletar todos os email e senha
    $query = "SELECT email, senha, nome, id_usuario FROM usuario WHERE email = ?;";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $emailForm);
    $stmt->execute();

    $credential = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (empty($credential)){
        $credential = [];
    }

    desconecta();

    // se o usuário requisitado existir
    if (count($credential) > 0) {

        if (password_verify($senhaForm, $credential['senha'])){
    
            // define a sessão como logada e atribue o email
            $_SESSION['login'] = $credential['email'];
            $_SESSION['usuario'] = $credential['id_usuario'];
            
            header("Location: ../../pages/dashboard/dashboard.php");
            exit();
        } else {
            $msg = 'Senha incorreta! Tente novamente.';
        }

    } else {
        $msg = "Usuário não cadastrado ou dados inválidos!";
        header("Location: ../../pages/usuario/login.php?msg={$msg}");
    }
}
print_r('deu ruim');
header("Location: ../../pages/usuario/login.php?msg={$msg}");
exit();