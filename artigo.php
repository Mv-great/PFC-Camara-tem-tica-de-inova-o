<?php
// Incluir arquivo de conexão
include 'conexao.php';

// Verificar se o ID do artigo foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirecionar ou mostrar erro se o ID não for válido
    header("Location: index.php"); // Redireciona para a página inicial
    exit();
}

$artigo_id = intval($_GET['id']);

// Buscar o artigo no banco de dados
$sql = "SELECT a.*, u.nome as autor_nome, c.nome as categoria_nome
        FROM artigos a
        LEFT JOIN usuarios u ON a.autor_id = u.id
        LEFT JOIN categorias c ON a.categoria_id = c.id
        WHERE a.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artigo_id);
$stmt->execute();
$result = $stmt->get_result();
$artigo = $result->fetch_assoc();
$stmt->close();

// Se o artigo não for encontrado
if (!$artigo) {
    // Redirecionar ou mostrar erro
    header("Location: index.php"); // Redireciona para a página inicial
    exit();
}

// Buscar categorias para o menu de navegação
$sql_categorias = "SELECT nome FROM categorias";
$result_categorias = $conn->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artigo['titulo']); ?> - Câmara Temática de Inovação</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Estilos básicos para a página de artigo individual */
        .artigo-individual-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .artigo-individual-container h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .artigo-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            text-align: center;
        }
        .artigo-meta span {
            margin: 0 10px;
        }
        .artigo-conteudo {
            line-height: 1.8;
            font-size: 1.1em;
            color: #444;
            white-space: pre-wrap; /* Mantém quebras de linha do banco de dados */
        }
        .artigo-conteudo p {
            margin-bottom: 1em;
        }
    </style>
</head>
<body>
    <header>
        <nav class="main-nav">
            <ul class="nav-links">
                <?php
                // Resetar o ponteiro do resultado das categorias
                $result_categorias->data_seek(0);
                if ($result_categorias->num_rows > 0) {
                    while($row = $result_categorias->fetch_assoc()) {
                        // Links de categoria no menu principal
                        $categoria_nome = htmlspecialchars($row['nome']);
                        // Mapeamento para os links da lista
                        $link_map = [
                            'Notícias' => 'noticias_lista.php',
                            'Eventos' => 'eventos_lista.php',
                            'Projetos' => 'projetos_lista.php',
                        ];
                        $link = $link_map[$categoria_nome] ?? '#';
                        echo "<li><a href='{$link}'>{$categoria_nome}</a></li>";
                    }
                }
                ?>
            </ul>
            <button class="membros-btn">Membros</button>
            <a href="login.php" class="admin-btn">Login</a>
        </nav>
    </header>

    <main class="artigo-individual-container">
        <h1><?php echo htmlspecialchars($artigo['titulo']); ?></h1>
        
        <div class="artigo-meta">
            <span>Categoria: <?php echo htmlspecialchars($artigo['categoria_nome']); ?></span>
            <span>Por: <?php echo htmlspecialchars($artigo['autor_nome'] ?? 'Desconhecido'); ?></span>
            <?php if ($artigo['categoria_id'] == 2): // Eventos ?>
                <span>Data do Evento: <?php echo date('d/m/Y', strtotime($artigo['data_evento'])); ?></span>
            <?php else: ?>
                <span>Publicado em: <?php echo date('d/m/Y', strtotime($artigo['criado_em'])); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="artigo-conteudo">
            <?php echo nl2br(htmlspecialchars($artigo['conteudo'])); ?>
        </div>

        <p style="margin-top: 30px; text-align: center;">
            <a href="javascript:history.back()" class="artigo-link">← Voltar para a lista</a>
        </p>
    </main>

    </main>

    <footer>
        <div class="bottom-sections">
            <section class="documentos">
                <h3>Documentos</h3>
                <ul class="documentos-list">
                    <li><a href="#">Lorem ipsum dolor sit amet consectetur adipiscing elit</a></li>
                    <li><a href="#">Sed do eiusmod tempor incididunt ut labore et dolore</a></li>
                    <li><a href="#">Magna aliqua ut enim ad minim veniam quis nostrud</a></li>
                </ul>
            </section>

            <section class="contato">
                <h3>Contato</h3>
                <form class="contato-form">
                    <input type="text" placeholder="Nome completo" required>
                    <input type="email" placeholder="E-mail" required>
                    <textarea placeholder="Mensagem" rows="4" required></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </section>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
