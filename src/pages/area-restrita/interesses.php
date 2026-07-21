<?php
require __DIR__ . '/../../../backend/includes/guard.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

$usuario = usuario_atual();
$token = csrf_token();
$pdo = db();

$anuncioId = (int)($_GET['anuncio_id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, marca, modelo, ano_fabricacao FROM anuncios WHERE id = ? AND usuario_id = ?');
$stmt->execute([$anuncioId, $usuario['id']]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    header('Location: meus-anuncios.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nome, telefone, mensagem, criado_em FROM interesses WHERE anuncio_id = ? ORDER BY criado_em DESC');
$stmt->execute([$anuncioId]);
$interesses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interesses no Anúncio - veloCity</title>
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
        <div class="mb-4">
            <a href="meus-anuncios.php" class="btn btn-velocity-secondary btn-sm">&larr; Voltar para Meus Anúncios</a>
        </div>

        <h3 class="mb-4" style="color: var(--azul-escuro);">Interessados: <span class="text-velocity-accent"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo'] . ' ' . $anuncio['ano_fabricacao']) ?></span></h3>

        <?php if (!$interesses): ?>
            <p class="text-muted">Nenhum interesse recebido para este anúncio ainda.</p>
        <?php else: ?>
        <div class="list-group shadow-sm border-0">
            <?php foreach ($interesses as $interesse): ?>
            <div class="list-group-item list-group-item-action p-4 border-0 mb-2 rounded">
                <div class="d-flex w-100 justify-content-between mb-2">
                    <h5 class="mb-1 fw-bold" style="color: var(--azul-marinho);"><?= h($interesse['nome']) ?></h5>
                    <small class="text-muted">Recebido em <?= h(date('d/m/Y H:i', strtotime($interesse['criado_em']))) ?></small>
                </div>
                <p class="mb-1 text-dark"><strong>Telefone:</strong> <?= h($interesse['telefone']) ?></p>
                <p class="mb-3 text-muted">"<?= nl2br(h($interesse['mensagem'])) ?>"</p>
                <button class="btn btn-sm btn-outline-danger btn-excluir-interesse" data-id="<?= (int)$interesse['id'] ?>">Excluir Mensagem</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <footer class="bg-velocity-footer text-white text-center p-3 mt-auto">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>

    <script>
        window.CSRF_TOKEN = "<?= h($token) ?>";
    </script>
    <script src="../../scripts/forms.js"></script>
</body>
</html>
