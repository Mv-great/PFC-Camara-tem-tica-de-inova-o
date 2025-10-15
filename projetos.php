<?php
// Verificar autenticação
include 'auth_check.php';

// Incluir arquivo de conexão
include 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';

// Processar ações (adicionar, editar, excluir)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add' || $action == 'edit') {
        $titulo = trim($_POST['titulo']);
        $conteudo = trim($_POST['conteudo']);
        $slug = !empty($_POST['slug']) ? trim($_POST['slug']) : strtolower(str_replace(' ', '-', $titulo));
        $categoria_id = 3; // Projetos
        $autor_id = $_SESSION['user_id'];
        
        if (empty($titulo) || empty($conteudo)) {
            $mensagem = "Por favor, preencha todos os campos obrigatórios.";
            $tipo_mensagem = 'error';
        } else {
            if ($action == 'add') {
                $stmt = $conn->prepare("INSERT INTO artigos (titulo, slug, conteudo, categoria_id, autor_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssii", $titulo, $slug, $conteudo, $categoria_id, $autor_id);
                
                if ($stmt->execute()) {
                    $mensagem = "Projeto adicionado com sucesso!";
                    $tipo_mensagem = 'success';
                } else {
                    $mensagem = "Erro ao adicionar projeto: " . $stmt->error;
                    $tipo_mensagem = 'error';
                }
                $stmt->close();
            } elseif ($action == 'edit') {
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE artigos SET titulo = ?, slug = ?, conteudo = ? WHERE id = ? AND categoria_id = 3");
                $stmt->bind_param("sssi", $titulo, $slug, $conteudo, $id);
                
                if ($stmt->execute()) {
                    $mensagem = "Projeto atualizado com sucesso!";
                    $tipo_mensagem = 'success';
                } else {
                    $mensagem = "Erro ao atualizar projeto: " . $stmt->error;
                    $tipo_mensagem = 'error';
                }
                $stmt->close();
            }
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM artigos WHERE id = ? AND categoria_id = 3");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $mensagem = "Projeto excluído com sucesso!";
            $tipo_mensagem = 'success';
        } else {
            $mensagem = "Erro ao excluir projeto: " . $stmt->error;
            $tipo_mensagem = 'error';
        }
        $stmt->close();
    }
}

// Obter lista de projetos
$projetos = $conn->query("SELECT a.*, u.nome as autor_nome FROM artigos a LEFT JOIN usuarios u ON a.autor_id = u.id WHERE a.categoria_id = 3 ORDER BY a.criado_em DESC");

// Se estiver editando, buscar os dados
$editando = false;
$projeto_edit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $editando = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM artigos WHERE id = ? AND categoria_id = 3");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $projeto_edit = $result->fetch_assoc();
    $stmt->close();
}

$mostrar_form = isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Projetos - Painel Administrativo</title>
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
                <a href="projetos.php" class="nav-item active">
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
                <h1>Gerenciar Projetos</h1>
                <p>Adicione, edite ou exclua projetos do site</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <?php if ($mostrar_form): ?>
                <!-- Formulário de Adicionar/Editar -->
                <div class="form-container">
                    <h2><?php echo $editando ? 'Editar Projeto' : 'Novo Projeto'; ?></h2>
                    <form method="POST" action="projetos.php">
                        <input type="hidden" name="action" value="<?php echo $editando ? 'edit' : 'add'; ?>">
                        <?php if ($editando): ?>
                            <input type="hidden" name="id" value="<?php echo $projeto_edit['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" id="titulo" name="titulo" required 
                                   value="<?php echo $editando ? htmlspecialchars($projeto_edit['titulo']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug (URL amigável)</label>
                            <input type="text" id="slug" name="slug" 
                                   value="<?php echo $editando ? htmlspecialchars($projeto_edit['slug']) : ''; ?>"
                                   placeholder="Deixe em branco para gerar automaticamente">
                        </div>
                        
                        <div class="form-group">
                            <label for="conteudo">Descrição *</label>
                            <textarea id="conteudo" name="conteudo" required><?php echo $editando ? htmlspecialchars($projeto_edit['conteudo']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <?php echo $editando ? 'Atualizar' : 'Adicionar'; ?>
                            </button>
                            <a href="projetos.php" class="btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Lista de Projetos -->
                <div class="content-table">
                    <div class="table-header">
                        <h2>Todos os Projetos</h2>
                        <a href="projetos.php?action=add" class="btn-primary">+ Novo Projeto</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($projetos->num_rows > 0): ?>
                                <?php while($projeto = $projetos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $projeto['id']; ?></td>
                                        <td><?php echo htmlspecialchars($projeto['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($projeto['autor_nome'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($projeto['criado_em'])); ?></td>
                                        <td class="actions">
                                            <a href="projetos.php?action=edit&id=<?php echo $projeto['id']; ?>" class="btn-edit">Editar</a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este projeto?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $projeto['id']; ?>">
                                                <button type="submit" class="btn-delete">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        Nenhum projeto encontrado. <a href="projetos.php?action=add">Adicionar o primeiro projeto</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>

