<?php
// Incluir arquivo de conexão (se ainda não estiver incluído)
if (!isset($conn)) {
    include 'conexao.php';
}

// 1. Buscar o e-mail de contato
$email_contato = 'contato@camarainovacao.com.br'; // Padrão
$chave = 'email_contato';
$stmt = $conn->prepare("SELECT valor FROM configuracoes WHERE chave = ?");
if ($stmt) {
    $stmt->bind_param("s", $chave);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $email_contato = $row['valor'];
    }
    $stmt->close();
}

// 2. Buscar a lista de documentos
$documentos_lista = $conn->query("SELECT titulo, arquivo_path FROM documentos ORDER BY criado_em DESC LIMIT 5");
?>

    <footer>
        <div class="bottom-sections">
            <section class="documentos">
                <h3>Documentos</h3>
                <ul class="documentos-list">
                    <?php if ($documentos_lista && $documentos_lista->num_rows > 0): ?>
                        <?php while($doc = $documentos_lista->fetch_assoc()): ?>
                            <li><a href="<?php echo htmlspecialchars($doc['arquivo_path']); ?>" target="_blank"><?php echo htmlspecialchars($doc['titulo']); ?></a></li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Nenhum documento disponível.</li>
                    <?php endif; ?>
                </ul>
            </section>

            <section class="contato">
                <h3>Contato</h3>
                <p>Entre em contato conosco através do e-mail:</p>
                <p><a href="mailto:<?php echo htmlspecialchars($email_contato); ?>"><?php echo htmlspecialchars($email_contato); ?></a></p>
            </section>
        </div>
    </footer>
</body>
</html>
<?php 
// Fechar a conexão se ela foi aberta neste arquivo
if (isset($conn) && !isset($fechar_conexao_externa)) {
    $conn->close(); 
}
?>
