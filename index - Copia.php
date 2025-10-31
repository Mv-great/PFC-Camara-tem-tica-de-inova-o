<?php
// Incluir arquivo de conexão
include 'conexao.php';

// Buscar categorias
$sql = "SELECT nome FROM categorias";
$result = $conn->query($sql);

// Consulta para buscar todos os usuários
$sql = "SELECT nome, email, foto_perfil FROM usuarios ORDER BY nome ASC";
$result = $conn->query($sql);

$membros = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $membros[] = $row;
    }
}
$conn->close();
// Caminho para a imagem de perfil padrão
$default_profile_pic = 'images/default_profile.png';
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
            <a href="membros.php" class="membros-btn">Membros</a>
            <a href="login.php" class="admin-btn">Login</a>
        </nav>
    </header>

    <main>
       <div class="membros-container">
        <h1>Membros da Câmara Temática de Inovação</h1>
        
        <?php if (empty($membros)): ?>
            <p>Nenhum membro cadastrado.</p>
        <?php else: ?>
            <div class="membros-grid">
                <?php foreach ($membros as $membro): ?>
                    <div class="membro-card">
                        <?php
                        $foto_path = !empty($membro['foto_perfil']) && file_exists($membro['foto_perfil']) ? $membro['foto_perfil'] : $default_profile_pic;
                        ?>
                        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto de <?php echo htmlspecialchars($membro['nome']); ?>" class="membro-foto">
                        <h2 class="membro-nome"><?php echo htmlspecialchars($membro['nome']); ?></h2>
                        <p class="membro-email">contato:<?php echo htmlspecialchars($membro['email']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
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
