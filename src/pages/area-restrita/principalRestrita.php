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
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel — veloCity</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="../../styles/design.css" />
  <script src="../../scripts/scripts.js" defer></script>
</head>
<body>
  <a href="#main-content" class="skip-link">Pular para o conteúdo</a>

  <header class="navbar">
    <div class="navbar__inner">
      <button class="navbar__toggle" id="dropdownToggle" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>

      <a href="../public/index.html" class="navbar__brand"><em>veloCity</em></a>

      <nav class="navbar__links" id="dropdownMenu">
        <a href="../public/index.html" class="navbar__link">Home</a>
        <a href="criar-anuncio.php" class="navbar__link">Anunciar</a>
        <a href="meus-anuncios.php" class="navbar__link">Meus Anúncios</a>
        <a href="principalRestrita.php" class="navbar__link navbar__link--active">Painel</a>
      </nav>
    </div>
  </header>

  <div class="toolbar">
    <div class="toolbar__inner">
      <nav class="toolbar__links">
        <a href="criar-anuncio.php" class="toolbar__link">Novo Anúncio</a>
        <a href="meus-anuncios.php" class="toolbar__link">Meus Anúncios</a>
      </nav>
      <form action="../../../backend/api/logout.php" method="post" style="margin:0;">
        <button type="submit" class="btn btn--sm btn--ghost" style="color:rgba(255,255,255,0.7);">
          <i class="bi bi-box-arrow-right"></i> Sair
        </button>
      </form>
    </div>
  </div>

  <main class="page dashboard" id="main-content">
    <div class="container">
      <div class="dashboard__welcome">
        <h1>Bem-vindo, <?= h($usuario['nome']) ?>!</h1>
        <p><?= h($usuario['email']) ?></p>
      </div>

      <div class="dashboard__grid">
        <div class="dashboard__card">
          <div class="dashboard__card-icon"><i class="bi bi-car-front"></i></div>
          <h3>Meus Anúncios</h3>
          <p><?= $totalAnuncios ?> anúncio<?= $totalAnuncios === 1 ? '' : 's' ?> ativo<?= $totalAnuncios === 1 ? '' : 's' ?></p>
          <a href="meus-anuncios.php" class="btn btn--primary btn--sm">Gerenciar</a>
        </div>

        <div class="dashboard__card">
          <div class="dashboard__card-icon"><i class="bi bi-bell"></i></div>
          <h3>Interesses</h3>
          <p><?= $totalInteresses ?> interesse<?= $totalInteresses === 1 ? '' : 's' ?> no total</p>
          <a href="meus-anuncios.php" class="btn btn--outline btn--sm">Visualizar</a>
        </div>

        <div class="dashboard__card">
          <div class="dashboard__card-icon"><i class="bi bi-plus-circle"></i></div>
          <h3>Novo Anúncio</h3>
          <p>Anuncie seu veículo</p>
          <a href="criar-anuncio.php" class="btn btn--outline btn--sm">Criar</a>
        </div>
      </div>

      <div class="activity">
        <h3 class="activity__title">Atividade Recente</h3>
        <?php if (!$atividades): ?>
          <p class="text-muted">Nenhum interesse recebido ainda.</p>
        <?php else: ?>
          <?php foreach ($atividades as $atividade): ?>
          <div class="activity__item">
            <span class="activity__date"><?= h(date('d/m/Y H:i', strtotime($atividade['criado_em']))) ?></span>
            <span class="activity__text"><?= h($atividade['nome']) ?> manifestou interesse em <?= h($atividade['marca'] . ' ' . $atividade['modelo'] . ' ' . $atividade['ano_fabricacao']) ?></span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <footer class="footer">
    &copy; 2026 veloCity — Todos os direitos reservados.
  </footer>
</body>
</html>
