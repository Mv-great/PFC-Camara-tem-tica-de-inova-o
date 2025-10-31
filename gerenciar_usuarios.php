<?php
// Verificar autenticação
include 'auth_check.php';

// Verificar se é administrador
if ($_SESSION['user_tipo'] != 'admin') {
    die("Acesso negado. Apenas administradores podem gerenciar usuários.");
}

// Incluir arquivo de conexão
include 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';

// Processar exclusão de usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['excluir_id'])) {
    $excluir_id = (int)$_POST['excluir_id'];
    $foto_perfil_path = $_POST['foto_perfil'];

    // Impedir que o administrador exclua a si mesmo
    if ($excluir_id == $_SESSION['user_id']) {
        $mensagem = "Erro: Você não pode excluir seu próprio perfil.";
        $tipo_mensagem = 'error';
    } else {
        // Iniciar transação (opcional, mas boa prática)
        // $conn->begin_transaction(); 

        // 1. Excluir a foto de perfil do servidor
        if (!empty($foto_perfil_path) && file_exists($foto_perfil_path)) {
            if (!unlink($foto_perfil_path)) {
                // Se a exclusão da foto falhar, apenas registra o erro e continua
                // $mensagem = "Aviso: Falha ao excluir a foto de perfil do servidor.";
            }
        }

        // 2. Excluir o usuário do banco de dados
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $excluir_id);

        if ($stmt->execute()) {
            $mensagem = "Usuário excluído com sucesso!";
            $tipo_mensagem = 'success';
            // $conn->commit();
        } else {
            $mensagem = "Erro ao excluir usuário: " . $stmt->error;
            $tipo_mensagem = 'error';
            // $conn->rollback();
        }
        $stmt->close();
    }
    
    // Redirecionar para evitar reenvio do formulário
    header("Location: gerenciar_usuarios.php?msg=" . urlencode($mensagem) . "&type=" . urlencode($tipo_mensagem));
    exit();
}

// Verifica se há mensagem na URL após redirecionamento
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $mensagem = htmlspecialchars($_GET['msg']);
    $tipo_mensagem = htmlspecialchars($_GET['type']);
}

// Buscar todos os usuários
$sql = "SELECT id, nome, email, tipo, foto_perfil FROM usuarios ORDER BY nome ASC";
$result = $conn->query($sql);

$usuarios = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Painel Administrativo</title>
    <link rel="stylesheet" href="css/admin.css">
    <script>
        function confirmarExclusao(nome) {
            return confirm("Tem certeza que deseja excluir o usuário: " + nome + "? Esta ação é irreversível.");
        }
    </script>
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
      <a href="gerenciar_usuarios.php" class="nav-item">
	                    <span class="icon">👥</span>
	                    Gerenciar Usuários
	                </a>
            </nav>
           <div class="sidebar-footer">
<div class="user-info">
	                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_nome']); ?></span>
	                    <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_tipo']); ?></span>
	                </div>
	                <a href="editar_perfil.php" class="btn-edit-profile">Editar Perfil</a>
	                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Gerenciar Usuários</h1>
                <p>Lista de todos os usuários cadastrados no sistema</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div class="content-table">
                <div class="table-header">
                    <h2>Usuários</h2>
                    <a href="criar_usuario.php" class="btn-primary">Novo Usuário</a>
                </div>
                
                <?php if (empty($usuarios)): ?>
                    <p style="padding: 20px;">Nenhum usuário cadastrado.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $default_profile_pic = 'images/default_profile.png';
                                        $foto_path = !empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil']) ? $usuario['foto_perfil'] : $default_profile_pic;
                                        ?>
                                        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($usuario['tipo'])); ?></td>
                                    <td class="actions">
                                        <!-- O botão de exclusão será um formulário POST para segurança -->
                                        <form method="POST" action="" onsubmit="return confirmarExclusao('<?php echo htmlspecialchars($usuario['nome']); ?>');" style="display: inline;">
                                            <input type="hidden" name="excluir_id" value="<?php echo $usuario['id']; ?>">
                                            <input type="hidden" name="foto_perfil" value="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>">
                                            <button type="submit" class="btn-delete" <?php echo ($usuario['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
