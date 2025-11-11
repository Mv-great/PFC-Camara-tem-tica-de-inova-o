<?php
// Incluir arquivo de conexão (se ainda não estiver incluído)
if (!isset($conn)) {
    include 'conexao.php';
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
    <title><?php echo $titulo_pagina ?? 'Câmara Temática de Inovação - Assis Chateaubriand'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <?php if (isset($estilos_adicionais)): echo $estilos_adicionais; endif; ?>
</head>
<body>
    <header>
        <nav class="main-nav">
            <ul class="nav-links">
                <li><a href="index.php">Início</a></li>
                <?php
                if ($result_categorias->num_rows > 0) {
                    // Resetar o ponteiro do resultado se necessário
                    if (isset($result_categorias->data_seek)) $result_categorias->data_seek(0);
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
            <a href="membros.php" class="membros-btn">Membros</a>
            <a href="login.php" class="admin-btn">Login</a>
        </nav>
    </header>
    <main>
