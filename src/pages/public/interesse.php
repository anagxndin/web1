<?php
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/csrf.php';
require_once __DIR__ . '/../../../backend/includes/functions.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

iniciar_sessao();
$token = csrf_token();

$anuncioId = (int)($_GET['id'] ?? 0);
$pdo = db();

$stmt = $pdo->prepare(
    'SELECT a.id, a.marca, a.modelo, a.ano_fabricacao, a.cidade, a.estado, a.valor, a.descricao,
            (SELECT f.caminho FROM anuncio_fotos f WHERE f.anuncio_id = a.id ORDER BY f.id ASC LIMIT 1) AS foto
     FROM anuncios a WHERE a.id = ?'
);
$stmt->execute([$anuncioId]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    header('Location: index.html');
    exit;
}

$fotoUrl = $anuncio['foto'] ? '../../' . $anuncio['foto'] : '../../assets/images/carLogo.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Interesse</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="../../styles/estilos.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <script src="../../scripts/scripts.js"></script>
    <script src="../../scripts/forms.js"></script>
</head>
<body class="pageWrapper">
    <header class="mainHeader">
      <button id="dropdownToggle"><i class="bi bi-list" id="menuIcon"></i></button>
      <div id="logo">
        <h1><i>veloCity</i></h1>
      </div>
      <nav class="dropdownMenu" id="dropdownMenu">
       <a href="index.html" class="dropdownItem">Home</a>
        <a href="../area-restrita/principalRestrita.php" class="dropdownItem">Home | Vendedor (Restrita a usuários)</a>
      </nav>

      <img src="../../assets/images/carLogo.png" alt="Logo" class="logoCar"/>
    </header>

    <nav class="publicNavBar">
        <a href="login.php" class="publicNavItem">Login</a>
        <a href="cadastro.php" class="publicNavItem">Cadastro</a>
    </nav>

    <main class="mainContent">
        <div class="interesseContainer">
            <h2>Deixe sua Mensagem de Interesse</h2>

            <div id="formAlert" class="formAlert" style="display:none;"></div>

            <div class="veiculoInteresse">
                <img src="<?= h($fotoUrl) ?>" alt="<?= h($anuncio['marca'] . ' ' . $anuncio['modelo']) ?>" class="veiculoImage">
                <div class="veiculoInfo">
                    <div class="veiculoHeader">
                        <h3 class="veiculoMarcaModelo"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo']) ?></h3>
                        <span class="veiculoAno"><?= (int)$anuncio['ano_fabricacao'] ?></span>
                    </div>
                    <p class="veiculoCidade"><i class="bi bi-geo-alt"></i> <?= h($anuncio['cidade'] . ', ' . $anuncio['estado']) ?></p>
                    <p class="veiculoValor">R$ <?= number_format((float)$anuncio['valor'], 2, ',', '.') ?></p>
                    <p class="veiculoDescricao"><?= h($anuncio['descricao']) ?></p>
                </div>
            </div>

            <form class="interesseForm" id="formInteresse" action="../../../backend/api/interesse_criar.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                <input type="hidden" name="anuncio_id" value="<?= (int)$anuncio['id'] ?>">
                <div class="formGroup">
                    <label for="interesseNome">Nome Completo</label>
                    <input type="text" id="interesseNome" name="nome" placeholder="Digite seu nome completo" required>
                </div>

                <div class="formGroup">
                    <label for="interesseTelefone">Telefone</label>
                    <input type="tel" id="interesseTelefone" name="telefone" placeholder="(XX) XXXXX-XXXX" required>
                </div>

                <div class="formGroup">
                    <label for="interesseMensagem">Mensagem de Interesse</label>
                    <textarea id="interesseMensagem" name="mensagem" placeholder="Deixe sua mensagem aqui..." rows="6" required></textarea>
                </div>

                <button type="submit" class="submitBtn">Enviar Interesse</button>
            </form>
        </div>
    </main>

    <footer class="bg-velocity-footer simpleFooter">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>
</body>
</html>
