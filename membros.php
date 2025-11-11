<?php
// Incluir arquivo de conexão
include 'conexao.php';

// Buscar categorias para o menu de navegação
$sql_categorias = "SELECT nome FROM categorias";
$result_categorias = $conn->query($sql_categorias);

// Buscar todos os usuários (membros)
$sql_membros = "SELECT nome, tipo FROM usuarios ORDER BY nome ASC";
$result_membros = $conn->query($sql_membros);

$titulo_pagina = 'Membros - Câmara Temática de Inovação';
$estilos_adicionais = '
    <style>
        /* Estilos específicos para a página de Membros */
        .membros-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            min-height: 50vh; /* Garante que o footer não suba muito */
        }
        .membros-container h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }
        .membros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .membro-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .membro-card h3 {
            margin-top: 0;
            color: #0056b3;
        }
        .membro-card p {
            color: #666;
            font-size: 0.9em;
        }
    </style>
';
include 'header.php';
?>
    <div class="membros-container">
        <h1>Membros da Câmara Temática de Inovação</h1>
        
        <p style="text-align: center; margin-bottom: 50px;">
            Aqui você pode listar os membros da câmara, suas funções e a instituição que representam.
        </p>

        <div class="membros-grid">
            <?php if ($result_membros->num_rows > 0): ?>
                <?php while($membro = $result_membros->fetch_assoc()): ?>
                    <div class="membro-card">
                        <h3><?php echo htmlspecialchars($membro['nome']); ?></h3>
                        <p>Função: <?php echo htmlspecialchars($membro['tipo']); ?></p>
                        <!-- Se houver uma coluna para instituição, ela seria adicionada aqui -->
                        <!-- <p>Instituição: [Instituição]</p> -->
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1 / -1;">Nenhum membro cadastrado.</p>
            <?php endif; ?>
        </div>
    </main>

    </div>
<?php 
$fechar_conexao_externa = true; // Evita que footer.php feche a conexão
include 'footer.php'; 
$conn->close();
?>
