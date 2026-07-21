<?php
require __DIR__ . '/../../../backend/includes/guard.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

$usuario = usuario_atual();
$pdo = db();

$anuncioId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM anuncios WHERE id = ? AND usuario_id = ?');
$stmt->execute([$anuncioId, $usuario['id']]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    header('Location: meus-anuncios.php');
    exit;
}

$stmt = $pdo->prepare('SELECT caminho FROM anuncio_fotos WHERE anuncio_id = ? ORDER BY id ASC');
$stmt->execute([$anuncioId]);
$fotos = array_column($stmt->fetchAll(), 'caminho');
if (!$fotos) {
    $fotos = ['https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&q=80&w=600&h=400'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Veículo - veloCity</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/style.css">
     <link rel="stylesheet" href="../../styles/estilos.css">
     <script src="../../scripts/scripts.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
     <header class="mainHeader">
        <button id="dropdownToggle"><i class="bi bi-list" id="menuIcon"></i></button>
        <div id="logo">
            <h1><i>veloCity</i></h1>
        </div>
        <nav class="dropdownMenu" id="dropdownMenu">
            <a href="../public/index.html" class="dropdownItem">Home</a>
            <a href="criar-anuncio.php" class="dropdownItem">Criar Anúncio</a>
            <a href="meus-anuncios.php" class="dropdownItem">Listagem de Anúncios</a>
            <a href="principalRestrita.php" class="dropdownItem">Home | Vendedor</a>
        </nav>

        <img src="../../assets/images/carLogo.png" alt="Logo" class="logoCar"/>
    </header>

    <div class="navbar navbar-expand-lg navbar-dark bg-velocity-nav">
        <div class="container">
            <a class="navbar-brand logo-text" href="meus-anuncios.php">Painel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="criar-anuncio.php">Novo Anúncio</a></li>
                    <li class="nav-item"><a class="nav-link" href="meus-anuncios.php">Meus Anúncios</a></li>
                </ul>
                <a class="btn btn-outline-light btn-sm" href="../../../backend/api/logout.php">Sair / Logoff</a>
            </div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <div class="mb-3">
            <a href="meus-anuncios.php" class="btn btn-velocity-secondary btn-sm">&larr; Voltar para Meus Anúncios</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h3 class="mb-0" style="color: var(--azul-escuro);"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo'] . ' - ' . $anuncio['ano_fabricacao']) ?></h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <?php $fotoPrincipal = strpos($fotos[0], 'http') === 0 ? $fotos[0] : '../../../' . $fotos[0]; ?>
                        <img src="<?= h($fotoPrincipal) ?>" class="img-fluid rounded mb-2" alt="Foto principal">
                        <?php if (count($fotos) > 1): ?>
                        <div class="d-flex gap-2">
                            <?php foreach (array_slice($fotos, 1) as $foto): ?>
                            <img src="<?= h(strpos($foto, 'http') === 0 ? $foto : '../../../' . $foto) ?>" class="img-thumbnail" alt="Foto do veículo" style="width: 30%;">
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-velocity-accent fs-3 mb-4">R$ <?= number_format((float)$anuncio['valor'], 2, ',', '.') ?></h4>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item bg-transparent"><strong>Marca:</strong> <?= h($anuncio['marca']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Modelo:</strong> <?= h($anuncio['modelo']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Ano de Fabricação:</strong> <?= (int)$anuncio['ano_fabricacao'] ?></li>
                            <li class="list-group-item bg-transparent"><strong>Cor:</strong> <?= h($anuncio['cor']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Quilometragem:</strong> <?= number_format((float)$anuncio['quilometragem'], 0, ',', '.') ?> km</li>
                            <li class="list-group-item bg-transparent"><strong>Localização:</strong> <?= h($anuncio['cidade'] . ' - ' . $anuncio['estado']) ?></li>
                        </ul>
                        <h5 style="color: var(--azul-marinho);">Descrição do Anunciante:</h5>
                        <p class="text-muted"><?= nl2br(h($anuncio['descricao'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-velocity-footer text-white text-center p-3 mt-auto">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>
</body>
</html>
