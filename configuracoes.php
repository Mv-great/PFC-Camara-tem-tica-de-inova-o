<?php
// Verificar autenticação
include 'auth_check.php';

// Incluir arquivo de conexão
include 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';
$email_contato = '';

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_contato_novo = trim($_POST['email_contato']);
    $chave = 'email_contato';
    
    if (empty($email_contato_novo) || !filter_var($email_contato_novo, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Por favor, insira um e-mail de contato válido.";
        $tipo_mensagem = 'error';
    } else {
        // Tentar atualizar ou inserir
        $stmt = $conn->prepare("INSERT INTO configuracoes (chave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?");
        $stmt->bind_param("sss", $chave, $email_contato_novo, $email_contato_novo);
        
        if ($stmt->execute()) {
            $mensagem = "E-mail de contato atualizado com sucesso!";
            $tipo_mensagem = 'success';
        } else {
            $mensagem = "Erro ao atualizar e-mail de contato: " . $stmt->error;
            $tipo_mensagem = 'error';
        }
        $stmt->close();
    }
}

// Obter o e-mail de contato atual
$chave = 'email_contato';
$stmt = $conn->prepare("SELECT valor FROM configuracoes WHERE chave = ?");
$stmt->bind_param("s", $chave);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $email_contato = $row['valor'];
} else {
    $email_contato = 'contato@camarainovacao.com.br'; // Valor padrão se não estiver no banco
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Painel Administrativo</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Painel Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span class="icon">📊</span>
                    Dashboard
                </a>
                <a href="noticias.php" class="nav-item">
                    <span class="icon">📰</span>
                    Notícias
                </a>
                <a href="eventos.php" class="nav-item">
                    <span class="icon">📅</span>
                    Eventos
                </a>
                <a href="projetos.php" class="nav-item">
                    <span class="icon">🚀</span>
                    Projetos
                </a>
                <a href="documentos.php" class="nav-item">
                    <span class="icon">📄</span>
                    Documentos
                </a>
                <a href="configuracoes.php" class="nav-item active">
                    <span class="icon">⚙️</span>
                    Configurações
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_nome']); ?></span>
                    <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_tipo']); ?></span>
                </div>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Configurações do Site</h1>
                <p>Gerencie as configurações globais do site.</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de E-mail de Contato -->
            <div class="form-container">
                <h2>E-mail de Contato</h2>
                <form method="POST" action="configuracoes.php">
                    
                    <div class="form-group">
                        <label for="email_contato">E-mail de Contato Exibido no Rodapé *</label>
                        <input type="email" id="email_contato" name="email_contato" required 
                               value="<?php echo htmlspecialchars($email_contato); ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Salvar Configuração</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
