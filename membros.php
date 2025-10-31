<?php
include 'conexao.php';

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
    <title>Membros da Câmara Temática</title>
    <!-- Tentativa de usar o CSS existente para manter o estilo -->
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .membros-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .membros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .membro-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        .membro-card:hover {
            transform: translateY(-5px);
        }
        .membro-foto {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #007bff; /* Cor primária do tema */
        }
        .membro-nome {
            font-size: 1.5em;
            margin: 0 0 5px 0;
            color: #333;
        }
        .membro-email {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
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
</body>
</html>
