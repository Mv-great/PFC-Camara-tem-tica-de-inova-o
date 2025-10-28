<?php
$host = "localhost";  // Servidor
$usuario = "root";    // Usuário do banco
$senha = "";          // Senha
$banco = "marcus"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

//echo "Conectado com sucesso!";

// Fechar conexão
//$conn->close();
?>