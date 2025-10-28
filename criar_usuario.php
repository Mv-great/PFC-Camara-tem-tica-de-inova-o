<?php
// Verificar autenticação
include 'auth_check.php';

// Verificar se é administrador
if ($_SESSION['user_tipo'] != 'admin') {
    die("Acesso negado. Apenas administradores podem criar usuários.");
}

// Incluir arquivo de conexão
include 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    
    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_mensagem = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
        $tipo_mensagem = 'error';
    } else {
        // Verificar se o e-mail já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado.";
            $tipo_mensagem = 'error';
        } else {
            // Hash da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Inserir usuário
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);
            
            if ($stmt->execute()) {
                $mensagem = "Usuário criado com sucesso!";
                $tipo_mensagem = 'success';
            } else {
                $mensagem = "Erro ao criar usuário: " . $stmt->error;
                $tipo_mensagem = 'error';
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário - Painel Administrativo</title>
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
                <h1>Criar Novo Usuário</h1>
                <p>Adicione um novo usuário ao sistema</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" required minlength="6">
                        <small style="color: #666; font-size: 12px;">Mínimo de 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo">Tipo de Usuário *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Criar Usuário</button>
                        <a href="dashboard.php" class="btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

