<?php
// Incluir arquivo de conexão
include 'conexao.php';

// Buscar categorias
$sql = "SELECT nome FROM categorias";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Câmara Temática de Inovação - Assis Chateaubriand</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <ul class="nav-links">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<li><a href='#{$row['nome']}'>{$row['nome']}</a></li>";
                    }
                }
                ?>
            </ul>
            <button class="membros-btn">Membros</button>
            <a href="login.php" class="admin-btn">Login</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>CÂMARA TEMÁTICA DE INOVAÇÃO</h1>
            <h2>ASSIS CHATEAUBRIAND</h2>
            
            <div class="hero-content">
                <div class="hero-image">
                    <img src="images/grupo.jpg" alt="Logo Câmara de Inovação">
                </div>
                <div class="sobre-box">
                    <h3>Sobre</h3>
                    <p>Somos um grupo diverso, formado por representantes da Prefeitura, do Instituto Federal do Paraná (IFPR), da Unimeo, do Núcleo Regional de Educação, da ACIAC, da OAB, de cooperativas de crédito e de diversas secretarias municipais.</p> 
                    <p>Acreditamos que a colaboração entre diferentes saberes é a chave para gerar soluções criativas para os desafios locais e promover um crescimento sustentável para todos. Junte-se a nós na construção de um Assis Chateaubriand mais inovador, competitivo e preparado para o futuro.</p>
                </div>
            </div>
        </section>

        <?php
        // Notícias
        $sql = "SELECT a.*, u.nome as autor_nome 
                FROM artigos a 
                LEFT JOIN usuarios u ON a.autor_id = u.id 
                WHERE a.categoria_id = 1 
                ORDER BY a.criado_em DESC";
        $result = $conn->query($sql);
        ?>
        <section id="Noticias" class="noticias">
            <h3>Notícias</h3>
            <div class="noticias-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "
                        <article class='noticia-item'>
                            <div class='noticia-image'></div>
                            <div class='noticia-content'>
                                <h4>{$row['titulo']}</h4>
                                <p class='autor-info'>Por: " . htmlspecialchars($row['autor_nome'] ?? 'Desconhecido') . "</p>
                                <p>{$row['conteudo']}</p>
                            </div>
                        </article>";
                    }
                }
                ?>
            </div>
            <a href="#" class="ver-todas">Ver todas as notícias</a>
        </section>

        <?php
        // Eventos
        $sql = "SELECT a.*, u.nome as autor_nome 
                FROM artigos a 
                LEFT JOIN usuarios u ON a.autor_id = u.id 
                WHERE a.categoria_id = 2 
                ORDER BY a.data_evento DESC";
        $result = $conn->query($sql);
        ?>
        <section id="Eventos" class="eventos">
            <h3>Próximos Eventos</h3>
            <div class="eventos-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "
                        <div class='evento-item'>
                            <div class='evento-icon'>📅</div>
                            <div class='evento-info'>
                                <div class='evento-data'>{$row['data_evento']}</div>
                                <div class='evento-titulo'><p>{$row['titulo']}</p></div>
                                <p class='autor-info'>Por: " . htmlspecialchars($row['autor_nome'] ?? 'Desconhecido') . "</p>
                                <a href='evento.php?cd={$row['id']}' class='ver-todos'>Ver evento</a>
                            </div>
                        </div>";
                    }
                }
                ?>
            </div>
            <a href="eventos.php" class="ver-todos">Ver todos os eventos</a>
        </section>

        <?php
        // Projetos
        $sql = "SELECT a.*, u.nome as autor_nome 
                FROM artigos a 
                LEFT JOIN usuarios u ON a.autor_id = u.id 
                WHERE a.categoria_id = 3 
                ORDER BY a.criado_em DESC";
        $result = $conn->query($sql);
        ?>
        <section id="Projetos" class="projetos">
            <h3>Projetos</h3>
            <div class="projetos-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "
                        <article class='projeto-item'>
                            <div class='projeto-image'></div>
                            <div class='projeto-content'>
                                <h4>{$row['titulo']}</h4>
                                <p class='autor-info'>Por: " . htmlspecialchars($row['autor_nome'] ?? 'Desconhecido') . "</p>
                                <p>{$row['conteudo']}</p>
                            </div>
                        </article>";
                    }
                }
                ?>
            </div>
            <a href="#" class="ver-todos">Ver todos os projetos</a>
        </section>
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
