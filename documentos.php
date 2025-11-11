<?php
// Verificar autenticação
include 'auth_check.php';

// Incluir arquivo de conexão
include 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';

// Processar ações (adicionar, excluir)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        $titulo = trim($_POST['titulo']);
        
        if (empty($titulo) || empty($_FILES['arquivo']['name'])) {
            $mensagem = "Por favor, preencha o título e selecione um arquivo.";
            $tipo_mensagem = 'error';
        } else {
            // Processar upload do arquivo
            $target_dir = "uploads/documentos/";
            // Criar diretório se não existir
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_name = basename($_FILES["arquivo"]["name"]);
            $target_file = $target_dir . time() . "_" . $file_name;
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Verifica se o arquivo é um documento (PDF, DOCX, etc.)
            if($fileType != "pdf" && $fileType != "doc" && $fileType != "docx" && $fileType != "xls" && $fileType != "xlsx") {
                $mensagem = "Desculpe, apenas arquivos PDF, DOC, DOCX, XLS e XLSX são permitidos.";
                $tipo_mensagem = 'error';
                $uploadOk = 0;
            }

            // Verifica se $uploadOk está definido como 0 por um erro
            if ($uploadOk == 0) {
                // A mensagem de erro já foi definida
            } else {
                if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $target_file)) {
                    // Inserir no banco de dados
                    $stmt = $conn->prepare("INSERT INTO documentos (titulo, arquivo_path) VALUES (?, ?)");
                    $stmt->bind_param("ss", $titulo, $target_file);
                    
                    if ($stmt->execute()) {
                        $mensagem = "Documento adicionado com sucesso!";
                        $tipo_mensagem = 'success';
                    } else {
                        $mensagem = "Erro ao adicionar documento ao banco de dados: " . $stmt->error;
                        $tipo_mensagem = 'error';
                        // Se falhar, tentar remover o arquivo
                        unlink($target_file);
                    }
                    $stmt->close();
                } else {
                    $mensagem = "Erro ao fazer upload do arquivo.";
                    $tipo_mensagem = 'error';
                }
            }
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        
        // 1. Obter o caminho do arquivo para exclusão
        $stmt = $conn->prepare("SELECT arquivo_path FROM documentos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $documento = $result->fetch_assoc();
        $stmt->close();
        
        if ($documento) {
            // 2. Excluir do banco de dados
            $stmt = $conn->prepare("DELETE FROM documentos WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                // 3. Excluir o arquivo físico
                if (file_exists($documento['arquivo_path'])) {
                    unlink($documento['arquivo_path']);
                }
                $mensagem = "Documento excluído com sucesso!";
                $tipo_mensagem = 'success';
            } else {
                $mensagem = "Erro ao excluir documento do banco de dados: " . $stmt->error;
                $tipo_mensagem = 'error';
            }
            $stmt->close();
        } else {
            $mensagem = "Documento não encontrado.";
            $tipo_mensagem = 'error';
        }
    }
}

// Obter lista de documentos
$documentos = $conn->query("SELECT * FROM documentos ORDER BY criado_em DESC");

$mostrar_form = isset($_GET['action']) && $_GET['action'] == 'add';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Documentos - Painel Administrativo</title>
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
                <a href="documentos.php" class="nav-item active">
                    <span class="icon">📄</span>
                    Documentos
                </a>
                <a href="configuracoes.php" class="nav-item">
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
                <h1>Gerenciar Documentos</h1>
                <p>Adicione ou exclua documentos para exibição no rodapé do site</p>
            </header>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <?php if ($mostrar_form): ?>
                <!-- Formulário de Adicionar -->
                <div class="form-container">
                    <h2>Novo Documento</h2>
                    <form method="POST" action="documentos.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label for="titulo">Título do Documento *</label>
                            <input type="text" id="titulo" name="titulo" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="arquivo">Arquivo (PDF, DOCX, etc.) *</label>
                            <input type="file" id="arquivo" name="arquivo" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Adicionar Documento</button>
                            <a href="documentos.php" class="btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Lista de Documentos -->
                <div class="content-table">
                    <div class="table-header">
                        <h2>Todos os Documentos</h2>
                        <a href="documentos.php?action=add" class="btn-primary">+ Novo Documento</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Arquivo</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($documentos->num_rows > 0): ?>
                                <?php while($documento = $documentos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $documento['id']; ?></td>
                                        <td><?php echo htmlspecialchars($documento['titulo']); ?></td>
                                        <td><a href="<?php echo htmlspecialchars($documento['arquivo_path']); ?>" target="_blank">Ver Arquivo</a></td>
                                        <td><?php echo date('d/m/Y', strtotime($documento['criado_em'])); ?></td>
                                        <td class="actions">
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este documento?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $documento['id']; ?>">
                                                <button type="submit" class="btn-delete">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        Nenhum documento encontrado. <a href="documentos.php?action=add">Adicionar o primeiro documento</a>
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
