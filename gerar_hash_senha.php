<?php
/**
 * Script auxiliar para gerar hash de senhas
 * Use este script para gerar hashes de senhas que podem ser inseridos diretamente no banco de dados
 * 
 * IMPORTANTE: Este arquivo deve ser removido ou protegido em produção!
 */

// Defina a senha que deseja gerar o hash
$senha = "a12"; // Altere esta senha

// Gerar o hash
$hash = password_hash($senha, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Hash de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        .hash-result {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerador de Hash de Senha</h1>
        
        <div class="warning">
            <strong>⚠️ ATENÇÃO:</strong> Este arquivo deve ser removido ou protegido em ambiente de produção!
        </div>
        
        <div class="info">
            <strong>Senha:</strong> <?php echo htmlspecialchars($senha); ?>
        </div>
        
        <div class="hash-result">
            <strong>Hash gerado:</strong><br>
            <?php echo $hash; ?>
        </div>
        
        <div class="info">
            <h3>Como usar:</h3>
            <ol>
                <li>Copie o hash gerado acima</li>
                <li>Execute o seguinte SQL no seu banco de dados:</li>
            </ol>
            <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;">
INSERT INTO usuarios (nome, email, senha, tipo) 
VALUES (
    'Nome do Usuário',
    'email@exemplo.com',
    '<?php echo $hash; ?>',
    'admin'
);</pre>
        </div>
        
        <div class="info">
            <h3>Para gerar outro hash:</h3>
            <p>Edite o arquivo <code>gerar_hash_senha.php</code> e altere o valor da variável <code>$senha</code> na linha 10.</p>
        </div>
    </div>
</body>
</html>

