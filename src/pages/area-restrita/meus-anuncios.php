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
  <title>Meus Anúncios — veloCity</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="../../styles/design.css" />
  <script src="../../scripts/scripts.js" defer></script>
  <script src="../../scripts/forms.js" defer></script>
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
        <a href="meus-anuncios.php" class="navbar__link navbar__link--active">Meus Anúncios</a>
        <a href="principalRestrita.php" class="navbar__link">Painel</a>
      </nav>
    </div>
  </header>

  <div class="toolbar">
    <div class="toolbar__inner">
      <nav class="toolbar__links">
        <a href="criar-anuncio.php" class="toolbar__link">Novo Anúncio</a>
        <a href="meus-anuncios.php" class="toolbar__link toolbar__link--active">Meus Anúncios</a>
      </nav>
      <form action="../../../backend/api/logout.php" method="post" style="margin:0;">
        <button type="submit" class="btn btn--sm btn--ghost" style="color:rgba(255,255,255,0.7);">
          <i class="bi bi-box-arrow-right"></i> Sair
        </button>
      </form>
    </div>
  </div>

  <main class="page page__main--sm" id="main-content">
    <div class="container">
      <div class="page-header">
        <h2 class="page-header__title">Meus Anúncios</h2>
        <a href="criar-anuncio.php" class="btn btn--primary">
          <i class="bi bi-plus-lg"></i> Novo Anúncio
        </a>
      </div>

      <?php if (!$anuncios): ?>
        <p class="text-muted">Você ainda não criou nenhum anúncio.</p>
      <?php else: ?>
      <div class="card-list">
        <?php foreach ($anuncios as $anuncio): ?>
        <div class="card-list__item">
          <img src="<?= $anuncio['foto'] ? h('../../../' . $anuncio['foto']) : '../../assets/images/carLogo.png' ?>" alt="Foto do veículo" class="card-list__image">
          <div class="card-list__body">
            <h3 class="card-list__title"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo']) ?></h3>
            <span class="card-list__meta">Ano: <?= (int)$anuncio['ano_fabricacao'] ?></span>
            <span class="card-list__price">R$ <?= number_format((float)$anuncio['valor'], 2, ',', '.') ?></span>
            <div class="card-list__actions">
              <a href="detalhes.php?id=<?= (int)$anuncio['id'] ?>" class="btn btn--primary btn--sm">Visualizar Detalhes</a>
              <a href="interesses.php?anuncio_id=<?= (int)$anuncio['id'] ?>" class="btn btn--outline btn--sm">Ver Interesses</a>
              <button class="btn btn--danger btn--sm btn-excluir-anuncio" data-id="<?= (int)$anuncio['id'] ?>">Excluir</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    window.CSRF_TOKEN = "<?= h($token) ?>";
  </script>
  <footer class="footer">
    &copy; 2026 veloCity — Todos os direitos reservados.
  </footer>
</body>
</html>
