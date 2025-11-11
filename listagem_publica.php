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
// Exemplo:
// $categoria_id = 1;
// $titulo_pagina = "Todas as Notícias";
// include 'listagem_publica.php';

// Se as variáveis não foram definidas, encerra com erro.
if (!isset($categoria_id) || !isset($titulo_pagina) || !isset($data_coluna)) {
    die("Erro: Categoria não definida. Verifique se \$categoria_id, \$titulo_pagina e \$data_coluna foram definidos no arquivo de inclusão.");
}

$dados = buscar_artigos($conn, $categoria_id, $titulo_pagina, $data_coluna);
$artigos = $dados['artigos'];
$titulo = $dados['titulo'];

// Buscar categorias para o menu de navegação
$sql_categorias = "SELECT nome FROM categorias";
$result_categorias = $conn->query($sql_categorias);

$titulo_pagina = htmlspecialchars($titulo);
$estilos_adicionais = '
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
';
include 'header.php';
?>
    <div class="listagem-container">
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

    </div>
<?php 
$fechar_conexao_externa = true; // Evita que footer.php feche a conexão
include 'footer.php'; 
$conn->close();
?>
