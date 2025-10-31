<?php
// Incluir arquivo de conexão
include 'conexao.php';

// Função para buscar artigos
function buscar_artigos($conn, $categoria_id, $titulo_pagina, $data_coluna = 'criado_em') {
    $sql = "SELECT a.*, u.nome as autor_nome 
            FROM artigos a 
            LEFT JOIN usuarios u ON a.autor_id = u.id 
            WHERE a.categoria_id = ?
            ORDER BY a.{$data_coluna} DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    return [
        'titulo' => $titulo_pagina,
        'artigos' => $result
    ];
}

// Variáveis que devem ser definidas nos arquivos de categoria (ex: noticias_lista.php)
$categoria_id = 0; // 1: Notícias, 2: Eventos, 3: Projetos
$titulo_pagina = "Lista de Artigos";
$data_coluna = 'criado_em'; // Coluna de ordenação, 'data_evento' para eventos

// O arquivo que inclui este template deve definir as variáveis acima e então incluir este arquivo.
// Exemplo:
// $categoria_id = 1;
// $titulo_pagina = "Todas as Notícias";
// include 'listagem_publica.php';

// Se as variáveis não foram definidas, não faz sentido continuar
if ($categoria_id === 0) {
    die("Erro: Categoria não definida.");
}

$dados = buscar_artigos($conn, $categoria_id, $titulo_pagina, $data_coluna);
$artigos = $dados['artigos'];
$titulo = $dados['titulo'];

// Buscar categorias para o menu de navegação
$sql_categorias = "SELECT nome FROM categorias";
$result_categorias = $conn->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?> - Câmara Temática de Inovação</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Estilos básicos para a página de listagem */
        .listagem-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .listagem-container h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }
        .artigo-item {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .artigo-item h2 {
            margin-top: 0;
            color: #0056b3;
        }
        .artigo-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
        }
        .artigo-meta span {
            margin-right: 15px;
        }
        .artigo-conteudo {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .artigo-link {
            display: inline-block;
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }
        .artigo-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <nav class="main-nav">
            <ul class="nav-links">
                <li><a href="index.php">Início</a></li>
                <?php
                if ($result_categorias->num_rows > 0) {
                    while($row = $result_categorias->fetch_assoc()) {
                        // Links de categoria no menu principal
                        $link_map = [
                            'Notícias' => 'noticias_lista.php',
                            'Eventos' => 'eventos_lista.php',
                            'Projetos' => 'projetos_lista.php',
                        ];
                        $categoria_nome = htmlspecialchars($row['nome']);
                        $link = $link_map[$categoria_nome] ?? '#'; // Fallback para #
                        echo "<li><a href='{$link}'>{$categoria_nome}</a></li>";
                    }
                }
                ?>
            </ul>
            <button class="membros-btn">Membros</button>
            <a href="login.php" class="admin-btn">Login</a>
        </nav>
    </header>

    <main class="listagem-container">
        <h1><?php echo htmlspecialchars($titulo); ?></h1>
        
        <?php if ($artigos->num_rows > 0): ?>
            <?php while($artigo = $artigos->fetch_assoc()): ?>
                <article class="artigo-item">
                    <h2><?php echo htmlspecialchars($artigo['titulo']); ?></h2>
                    <div class="artigo-meta">
                        <span>Por: <?php echo htmlspecialchars($artigo['autor_nome'] ?? 'Desconhecido'); ?></span>
                        <?php if ($categoria_id == 2): // Eventos ?>
                            <span>Data do Evento: <?php echo date('d/m/Y', strtotime($artigo['data_evento'])); ?></span>
                        <?php else: ?>
                            <span>Publicado em: <?php echo date('d/m/Y', strtotime($artigo['criado_em'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="artigo-conteudo">
                        <?php 
                            // Exibir um resumo do conteúdo
                            $resumo = substr(strip_tags($artigo['conteudo']), 0, 300);
                            if (strlen($artigo['conteudo']) > 300) {
                                $resumo .= '...';
                            }
                            echo nl2br(htmlspecialchars($resumo));
                        ?>
                    </div>
                    <!-- Link para a página de artigo individual, que não existe, mas mantemos o placeholder -->
                    <a href="artigo.php?id=<?php echo $artigo['id']; ?>" class="artigo-link">Leia mais</a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Nenhum artigo encontrado nesta categoria.</p>
        <?php endif; ?>

    </main>

    <footer>
        <!-- O rodapé completo de index.php seria incluído aqui para consistência -->
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
