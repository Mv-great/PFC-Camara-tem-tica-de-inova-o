<?php
    // Incluir arquivo de conexão
    include 'conexao.php';
    $sql = "SELECT nome FROM categorias";
    $result = $conn->query($sql);

    $cd=$_GET['cd'];
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
                <li><a href="index.php">Home</a></li>
            </ul>
            <button class="membros-btn">Membros</button>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>CÂMARA TEMÁTICA DE INOVAÇÃO</h1>
            <h2>ASSIS CHATEAUBRIAND</h2>
            </section>

        <section id="Eventos" class="eventos">
            <?php
                $sql = "SELECT * FROM artigos where categoria_id=2 && id=$cd";
                $result = $conn->query($sql);
            ?>

            
                        <?php
             if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "
                    <h3> $row[titulo] ($row[data_evento])</h3>
                        <p>$row[conteudo]</p>";
                }}      
        ?>      
        
         
        </section>

    <?php
            $sql = "SELECT * FROM artigos where categoria_id=3"; ;
            $result = $conn->query($sql);
         
            
            
           
        ?>

        


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
    </main>
</body>
</html>

