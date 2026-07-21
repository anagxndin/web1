<?php
require __DIR__ . '/../../../backend/includes/guard.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

$usuario = usuario_atual();
$pdo = db();

$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM anuncios WHERE usuario_id = ?');
$stmt->execute([$usuario['id']]);
$totalAnuncios = (int)$stmt->fetch()['total'];

$stmt = $pdo->prepare(
    'SELECT COUNT(*) AS total FROM interesses i
     JOIN anuncios a ON a.id = i.anuncio_id
     WHERE a.usuario_id = ?'
);
$stmt->execute([$usuario['id']]);
$totalInteresses = (int)$stmt->fetch()['total'];

$stmt = $pdo->prepare(
    'SELECT i.nome, i.criado_em, a.marca, a.modelo, a.ano_fabricacao
     FROM interesses i
     JOIN anuncios a ON a.id = i.anuncio_id
     WHERE a.usuario_id = ?
     ORDER BY i.criado_em DESC
     LIMIT 5'
);
$stmt->execute([$usuario['id']]);
$atividades = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="../../styles/estilos.css">
    <link rel="stylesheet" href="../../styles/homeRestrita.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <script src="../../scripts/scripts.js"></script>
</head>
<body id="homeBodyRestricted" class="d-flex flex-column min-vh-100">
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

    <main class="flex-grow-1">
        <div class="principalRestritaContainer">
            <div class="userWelcome">
                <h2>Bem-vindo, <?= h($usuario['nome']) ?>!</h2>
                <p class="userEmail"><?= h($usuario['email']) ?></p>
            </div>

            <div class="dashboardGrid">
                <div class="dashboardCard">
                    <div class="cardIcon">
                        <i class="bi bi-car-front"></i>
                    </div>
                    <h3>Meus Anúncios</h3>
                    <p class="cardStats"><?= $totalAnuncios ?> anúncio<?= $totalAnuncios === 1 ? '' : 's' ?> ativo<?= $totalAnuncios === 1 ? '' : 's' ?></p>
                    <a href="meus-anuncios.php" class="cardLink">Gerenciar Anúncios</a>
                </div>

                <div class="dashboardCard">
                    <div class="cardIcon">
                        <i class="bi bi-bell"></i>
                    </div>
                    <h3>Interesses Recebidos</h3>
                    <p class="cardStats"><?= $totalInteresses ?> interesse<?= $totalInteresses === 1 ? '' : 's' ?> no total</p>
                    <a href="meus-anuncios.php" class="cardLink">Ver Interesses</a>
                </div>

                <div class="dashboardCard">
                    <div class="cardIcon">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <h3>Criar Novo Anúncio</h3>
                    <p class="cardStats">Anuncie seu veículo</p>
                    <a href="criar-anuncio.php" class="cardLink">Novo Anúncio</a>
                </div>
            </div>

            <div class="recentActivity">
                <h3>Atividade Recente</h3>
                <?php if (!$atividades): ?>
                    <p class="text-muted">Nenhum interesse recebido ainda.</p>
                <?php else: ?>
                <ul class="activityList">
                    <?php foreach ($atividades as $atividade): ?>
                    <li>
                        <span class="activityDate"><?= h(date('d/m/Y H:i', strtotime($atividade['criado_em']))) ?></span>
                        <span class="activityText"><?= h($atividade['nome']) ?> manifestou interesse em <?= h($atividade['marca'] . ' ' . $atividade['modelo'] . ' ' . $atividade['ano_fabricacao']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-velocity-footer text-white text-center p-3 mt-auto">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>
</body>
</html>
