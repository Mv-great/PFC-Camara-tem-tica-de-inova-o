<?php
// Verificar autenticação
include 'auth_check.php';

// Incluir arquivo de conexão
include 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';

$user_id = $_SESSION['user_id'];

// 1. Carregar dados atuais do usuário
$stmt = $conn->prepare("SELECT nome, email, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    // Se por algum motivo o usuário não for encontrado (erro de sessão/banco)
    die("Erro: Usuário não encontrado.");
}

// 2. Processar formulário    // Processar remoção de foto
    if (isset($_POST['remover_foto'])) {
        if (!empty($usuario['foto_perfil'])) {
            // Deletar arquivo antigo
            if (file_exists($usuario['foto_perfil'])) {
                unlink($usuario['foto_perfil']);
            }
            
            // Atualizar banco de dados
            $stmt_update = $conn->prepare("UPDATE usuarios SET foto_perfil = NULL WHERE id = ?");
            $stmt_update->bind_param("i", $user_id);
            if ($stmt_update->execute()) {
                $mensagem = "Foto de perfil removida com sucesso!";
                $tipo_mensagem = 'success';
                $usuario['foto_perfil'] = NULL; // Atualiza a variável de sessão para refletir a mudança
            } else {
                $mensagem = "Erro ao remover foto: " . $stmt_update->error;
                $tipo_mensagem = 'error';
            }
            $stmt_update->close();
            // Redireciona para evitar reenvio do formulário
            header("Location: editar_perfil.php?msg=" . urlencode($mensagem) . "&type=" . urlencode($tipo_mensagem));
            exit();
        }
    }
    
    // Processar atualização de perfil
    if (isset($_POST['submit_perfil'])) {
        $novo_nome = trim($_POST['nome']);
        $nova_senha = $_POST['nova_senha'];
        $nova_foto_path = $usuario['foto_perfil']; // Mantém a foto atual por padrão
        
        if (empty($novo_nome)) {
            $mensagem = "O campo Nome Completo não pode ser vazio.";
            $tipo_mensagem = 'error';
        } else {
            // Processar upload de nova foto
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
                $target_dir = "uploads/perfil/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                
                if (in_array($file_extension, $allowed_extensions)) {
                    // Deletar foto antiga se existir
                    if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])) {
                        unlink($usuario['foto_perfil']);
                    }
                    
                    $new_file_name = uniqid('perfil_', true) . '.' . $file_extension;
                    $target_file = $target_dir . $new_file_name;
                    
                    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $target_file)) {
                        $nova_foto_path = $target_file;
                    } else {
                        $mensagem = "Erro ao fazer upload da nova foto.";
                        $tipo_mensagem = 'error';
                        goto end_update;
                    }
                } else {
                    $mensagem = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos para a foto.";
                    $tipo_mensagem = 'error';
                    goto end_update;
                }
            }
            
            // Construir a query de atualização
            $sql_parts = ["nome = ?"];
            $params = [$novo_nome];
            $types = "s";
            
            if (!empty($nova_senha)) {
                if (strlen($nova_senha) < 6) {
                    $mensagem = "A nova senha deve ter no mínimo 6 caracteres.";
                    $tipo_mensagem = 'error';
                    goto end_update;
                }
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql_parts[] = "senha = ?";
                $params[] = $senha_hash;
                $types .= "s";
            }
            
            $sql_parts[] = "foto_perfil = ?";
            $params[] = $nova_foto_path;
            $types .= "s";
            
            $sql = "UPDATE usuarios SET " . implode(", ", $sql_parts) . " WHERE id = ?";
            $params[] = $user_id;
            $types .= "i";
            
            $stmt_update = $conn->prepare($sql);
            $stmt_update->bind_param($types, ...$params);
            
            if ($stmt_update->execute()) {
                $mensagem = "Perfil atualizado com sucesso!";
                $tipo_mensagem = 'success';
                
                // Atualizar sessão e dados do usuário na página
                $_SESSION['user_nome'] = $novo_nome;
                $usuario['nome'] = $novo_nome;
                $usuario['foto_perfil'] = $nova_foto_path;
            } else {
                $mensagem = "Erro ao atualizar perfil: " . $stmt_update->error;
                $tipo_mensagem = 'error';
            }
            $stmt_update->close();
        }
    }
    
    end_update:
    // Não redireciona para evitar o loop, mas pode causar aviso de reenvio de formulário.


$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Painel Administrativo</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #004E64;
        }
        .form-group-photo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-remover-foto {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .btn-remover-foto:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (Mantendo a estrutura do criar_usuario.php) -->
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
                <h1>Editar Meu Perfil</h1>
                <p>Atualize suas informações e foto de perfil</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    
                    <div class="form-group-photo">
                        <?php
                        $default_profile_pic = 'images/default_profile.png'; // Assumindo que você criou essa imagem
                        $foto_path = !empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil']) ? $usuario['foto_perfil'] : $default_profile_pic;
                        ?>
                        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto de Perfil" class="profile-picture">
                        
                        <?php if (!empty($usuario['foto_perfil'])): ?>
                            <button type="submit" name="remover_foto" value="1" class="btn-remover-foto">Remover Foto Atual</button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail * (Não pode ser alterado)</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha (Deixe em branco para não alterar)</label>
                        <input type="password" id="nova_senha" name="nova_senha" minlength="6">
                        <small style="color: #666; font-size: 12px;">Mínimo de 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="foto_perfil">Alterar Foto de Perfil</label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                        <small style="color: #666; font-size: 12px;">Formatos aceitos: JPG, JPEG, PNG, GIF. O upload substituirá a foto atual.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="submit_perfil" class="btn-primary">Salvar Alterações</button>
                        <a href="dashboard.php" class="btn-secondary">Voltar ao Dashboard</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
