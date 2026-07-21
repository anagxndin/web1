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
  <title>Interesses — veloCity</title>
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
        <a href="meus-anuncios.php" class="navbar__link">Meus Anúncios</a>
        <a href="principalRestrita.php" class="navbar__link">Painel</a>
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

  <main class="page page__main--sm" id="main-content">
    <div class="container" style="max-width: 800px;">
      <a href="meus-anuncios.php" class="back-link">
        <i class="bi bi-arrow-left"></i> Voltar para Meus Anúncios
      </a>

      <div class="page-header">
        <h2 class="page-header__title">
          Interesses: <span style="color:var(--color-accent);"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo'] . ' ' . $anuncio['ano_fabricacao']) ?></span>
        </h2>
      </div>

      <?php if (!$interesses): ?>
        <p class="text-muted">Nenhum interesse recebido para este anúncio ainda.</p>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:1rem;">
        <?php foreach ($interesses as $interesse): ?>
        <div class="interest-item">
          <div class="interest-item__header">
            <span class="interest-item__name"><?= h($interesse['nome']) ?></span>
            <span class="interest-item__date">Recebido em <?= h(date('d/m/Y H:i', strtotime($interesse['criado_em']))) ?></span>
          </div>
          <div class="interest-item__phone">
            <i class="bi bi-telephone" style="color:var(--color-accent);margin-right:0.5rem;"></i>
            <?= h($interesse['telefone']) ?>
          </div>
          <div class="interest-item__message">
            "<?= nl2br(h($interesse['mensagem'])) ?>"
          </div>
          <button class="btn btn--danger btn--sm btn-excluir-interesse" data-id="<?= (int)$interesse['id'] ?>">
            <i class="bi bi-trash"></i> Excluir Mensagem
          </button>
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
