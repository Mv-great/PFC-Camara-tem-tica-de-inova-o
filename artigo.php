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

$titulo_pagina = htmlspecialchars($artigo['titulo']);
$estilos_adicionais = '
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
';
include 'header.php';
?>
    <div class="artigo-individual-container">
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

    </div>
<?php 
$fechar_conexao_externa = true; // Evita que footer.php feche a conexão
include 'footer.php'; 
$conn->close();
?>
