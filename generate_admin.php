<?php
include 'conexao.php';

$nome = 'admin_user';
$email = 'admin@example.com';
$senha_plana = 'admin_password';
$tipo = 'admin';

$senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);

if ($stmt->execute()) {
    echo "Usuário administrador criado com sucesso!\n";
} else {
    echo "Erro ao criar usuário administrador: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
