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
        $data_evento = $_POST['data_evento'];
        $slug = !empty($_POST['slug']) ? trim($_POST['slug']) : strtolower(str_replace(' ', '-', $titulo));
        $categoria_id = 2; // Eventos
        $autor_id = $_SESSION['user_id'];
        
        if (empty($titulo) || empty($conteudo) || empty($data_evento)) {
            $mensagem = "Por favor, preencha todos os campos obrigatórios.";
            $tipo_mensagem = 'error';
        } else {
            if ($action == 'add') {
                $stmt = $conn->prepare("INSERT INTO artigos (titulo, slug, conteudo, categoria_id, autor_id, data_evento) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssiis", $titulo, $slug, $conteudo, $categoria_id, $autor_id, $data_evento);
                
                if ($stmt->execute()) {
                    $mensagem = "Evento adicionado com sucesso!";
                    $tipo_mensagem = 'success';
                } else {
                    $mensagem = "Erro ao adicionar evento: " . $stmt->error;
                    $tipo_mensagem = 'error';
                }
                $stmt->close();
            } elseif ($action == 'edit') {
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE artigos SET titulo = ?, slug = ?, conteudo = ?, data_evento = ? WHERE id = ? AND categoria_id = 2");
                $stmt->bind_param("ssssi", $titulo, $slug, $conteudo, $data_evento, $id);
                
                if ($stmt->execute()) {
                    $mensagem = "Evento atualizado com sucesso!";
                    $tipo_mensagem = 'success';
                } else {
                    $mensagem = "Erro ao atualizar evento: " . $stmt->error;
                    $tipo_mensagem = 'error';
                }
                $stmt->close();
            }
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM artigos WHERE id = ? AND categoria_id = 2");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $mensagem = "Evento excluído com sucesso!";
            $tipo_mensagem = 'success';
        } else {
            $mensagem = "Erro ao excluir evento: " . $stmt->error;
            $tipo_mensagem = 'error';
        }
        $stmt->close();
    }
}

// Obter lista de eventos
$eventos = $conn->query("SELECT a.*, u.nome as autor_nome FROM artigos a LEFT JOIN usuarios u ON a.autor_id = u.id WHERE a.categoria_id = 2 ORDER BY a.data_evento DESC");

// Se estiver editando, buscar os dados
$editando = false;
$evento_edit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $editando = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM artigos WHERE id = ? AND categoria_id = 2");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $evento_edit = $result->fetch_assoc();
    $stmt->close();
}

$mostrar_form = isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eventos - Painel Administrativo</title>
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
                <a href="eventos.php" class="nav-item active">
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
                <h1>Gerenciar Eventos</h1>
                <p>Adicione, edite ou exclua eventos do site</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <?php if ($mostrar_form): ?>
                <!-- Formulário de Adicionar/Editar -->
                <div class="form-container">
                    <h2><?php echo $editando ? 'Editar Evento' : 'Novo Evento'; ?></h2>
                    <form method="POST" action="eventos.php">
                        <input type="hidden" name="action" value="<?php echo $editando ? 'edit' : 'add'; ?>">
                        <?php if ($editando): ?>
                            <input type="hidden" name="id" value="<?php echo $evento_edit['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" id="titulo" name="titulo" required 
                                   value="<?php echo $editando ? htmlspecialchars($evento_edit['titulo']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="data_evento">Data do Evento *</label>
                            <input type="date" id="data_evento" name="data_evento" required 
                                   value="<?php echo $editando ? $evento_edit['data_evento'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug (URL amigável)</label>
                            <input type="text" id="slug" name="slug" 
                                   value="<?php echo $editando ? htmlspecialchars($evento_edit['slug']) : ''; ?>"
                                   placeholder="Deixe em branco para gerar automaticamente">
                        </div>
                        
                        <div class="form-group">
                            <label for="conteudo">Descrição *</label>
                            <textarea id="conteudo" name="conteudo" required><?php echo $editando ? htmlspecialchars($evento_edit['conteudo']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <?php echo $editando ? 'Atualizar' : 'Adicionar'; ?>
                            </button>
                            <a href="eventos.php" class="btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Lista de Eventos -->
                <div class="content-table">
                    <div class="table-header">
                        <h2>Todos os Eventos</h2>
                        <a href="eventos.php?action=add" class="btn-primary">+ Novo Evento</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Data do Evento</th>
                                <th>Autor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($eventos->num_rows > 0): ?>
                                <?php while($evento = $eventos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $evento['id']; ?></td>
                                        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($evento['data_evento'])); ?></td>
                                        <td><?php echo htmlspecialchars($evento['autor_nome'] ?? 'N/A'); ?></td>
                                        <td class="actions">
                                            <a href="eventos.php?action=edit&id=<?php echo $evento['id']; ?>" class="btn-edit">Editar</a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                                                <button type="submit" class="btn-delete">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        Nenhum evento encontrado. <a href="eventos.php?action=add">Adicionar o primeiro evento</a>
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

