<?php
// Arquivo de verificação de autenticação
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_nome'])) {
    // Se não estiver logado, redirecionar para a página de login
    header("Location: login.php");
    exit();
}

// Verificar se a sessão expirou (opcional - 30 minutos de inatividade)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // Última requisição foi há mais de 30 minutos
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}

// Atualizar o timestamp da última atividade
$_SESSION['last_activity'] = time();
?>

