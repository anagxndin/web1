<?php
require __DIR__ . '/../../../backend/includes/guard.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

$usuario = usuario_atual();
$token = csrf_token();
$pdo = db();

$stmt = $pdo->prepare(
    'SELECT a.id, a.marca, a.modelo, a.ano_fabricacao, a.valor,
            (SELECT f.caminho FROM anuncio_fotos f WHERE f.anuncio_id = a.id ORDER BY f.id ASC LIMIT 1) AS foto
     FROM anuncios a
     WHERE a.usuario_id = ?
     ORDER BY a.criado_em DESC'
);
$stmt->execute([$usuario['id']]);
$anuncios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Anúncios - veloCity</title>
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
                    <li class="nav-item"><a class="nav-link active" href="meus-anuncios.php">Meus Anúncios</a></li>
                </ul>
                <a class="btn btn-outline-light btn-sm" href="../../../backend/api/logout.php">Sair / Logoff</a>
            </div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 style="color: var(--azul-escuro);">Meus Anúncios</h3>
            <a href="criar-anuncio.php" class="btn btn-velocity-primary"> + Criar Novo Anúncio</a>
        </div>

        <?php if (!$anuncios): ?>
            <p class="text-muted">Você ainda não criou nenhum anúncio.</p>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($anuncios as $anuncio): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <img src="<?= $anuncio['foto'] ? h('../../../' . $anuncio['foto']) : 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&q=80&w=400&h=250' ?>" class="card-img-top" alt="Foto do veículo" style="object-fit: cover; height: 200px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold" style="color: var(--azul-escuro);"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo']) ?></h5>
                        <p class="card-text text-muted mb-1">Ano: <?= (int)$anuncio['ano_fabricacao'] ?></p>
                        <p class="card-text text-velocity-accent fs-5 mb-3">R$ <?= number_format((float)$anuncio['valor'], 2, ',', '.') ?></p>

                        <div class="mt-auto d-flex flex-column gap-2">
                            <a href="detalhes.php?id=<?= (int)$anuncio['id'] ?>" class="btn btn-sm btn-velocity-secondary">Visualizar Detalhes</a>
                            <a href="interesses.php?anuncio_id=<?= (int)$anuncio['id'] ?>" class="btn btn-sm btn-outline-secondary">Ver Interesses</a>
                            <button class="btn btn-sm btn-outline-danger btn-excluir-anuncio" data-id="<?= (int)$anuncio['id'] ?>">Excluir Anúncio</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
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
