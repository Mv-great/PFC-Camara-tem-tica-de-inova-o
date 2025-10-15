<?php
// Verificar autenticação
include 'auth_check.php';

// Incluir arquivo de conexão
include 'conexao.php';

// Obter estatísticas
$total_noticias = $conn->query("SELECT COUNT(*) as total FROM artigos WHERE categoria_id = 1")->fetch_assoc()['total'];
$total_eventos = $conn->query("SELECT COUNT(*) as total FROM artigos WHERE categoria_id = 2")->fetch_assoc()['total'];
$total_projetos = $conn->query("SELECT COUNT(*) as total FROM artigos WHERE categoria_id = 3")->fetch_assoc()['total'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Painel Administrativo</title>
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
                <a href="dashboard.php" class="nav-item active">
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
                <h1>Dashboard</h1>
                <p>Bem-vindo ao painel administrativo da Câmara Temática de Inovação</p>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e3f2fd;">📰</div>
                    <div class="stat-info">
                        <h3><?php echo $total_noticias; ?></h3>
                        <p>Notícias</p>
                    </div>
                    <a href="noticias.php" class="stat-link">Gerenciar →</a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3e5f5;">📅</div>
                    <div class="stat-info">
                        <h3><?php echo $total_eventos; ?></h3>
                        <p>Eventos</p>
                    </div>
                    <a href="eventos.php" class="stat-link">Gerenciar →</a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #e8f5e9;">🚀</div>
                    <div class="stat-info">
                        <h3><?php echo $total_projetos; ?></h3>
                        <p>Projetos</p>
                    </div>
                    <a href="projetos.php" class="stat-link">Gerenciar →</a>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Ações Rápidas</h2>
                <div class="actions-grid">
                    <a href="noticias.php?action=add" class="action-card">
                        <span class="action-icon">➕</span>
                        <span class="action-text">Nova Notícia</span>
                    </a>
                    <a href="eventos.php?action=add" class="action-card">
                        <span class="action-icon">➕</span>
                        <span class="action-text">Novo Evento</span>
                    </a>
                    <a href="projetos.php?action=add" class="action-card">
                        <span class="action-icon">➕</span>
                        <span class="action-text">Novo Projeto</span>
                    </a>
                    <a href="index.php" class="action-card" target="_blank">
                        <span class="action-icon">🌐</span>
                        <span class="action-text">Ver Site</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

