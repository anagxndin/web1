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
    $fotos = ['../../assets/images/carLogo.png'];
} else {
    $fotos = array_map(function ($f) { return '../../../' . $f; }, $fotos);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalhes do Veículo — veloCity</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="../../styles/design.css" />
  <script src="../../scripts/scripts.js" defer></script>
  <script src="../../scripts/gallery.js" defer></script>
</head>
<body>
  <a href="#main-content" class="skip-link">Pular para o conteúdo</a>

  <header class="navbar">
    <div class="navbar__inner">
      <button class="navbar__toggle" id="dropdownToggle" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>

      <a href="../public/index.php" class="navbar__brand"><em>veloCity</em></a>

      <nav class="navbar__links" id="dropdownMenu">
        <a href="../public/index.php" class="navbar__link">Home</a>
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
    <div class="container" style="max-width: 960px;">
      <a href="meus-anuncios.php" class="back-link">
        <i class="bi bi-arrow-left"></i> Voltar para Meus Anúncios
      </a>

      <div class="details">
        <div class="details__header">
          <h2 style="font-size:var(--text-2xl);font-weight:700;"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo'] . ' - ' . $anuncio['ano_fabricacao']) ?></h2>
        </div>
        <div class="details__body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;">
            <div>
              <div class="details__gallery" data-photos="<?= h(json_encode($fotos)) ?>">
                <div class="details__gallery-main">
                  <img src="<?= h($fotos[0]) ?>" alt="Foto principal">
                </div>
                <?php if (count($fotos) > 1): ?>
                <div class="details__gallery-thumbs">
                  <?php foreach (array_slice($fotos, 1) as $foto): ?>
                  <img src="<?= h($foto) ?>" alt="Foto do veículo">
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div>
              <div class="details__price">R$ <?= number_format((float)$anuncio['valor'], 2, ',', '.') ?></div>

              <div class="details__specs">
                <div class="details__spec">
                  <div class="details__spec-label">Marca</div>
                  <div class="details__spec-value"><?= h($anuncio['marca']) ?></div>
                </div>
                <div class="details__spec">
                  <div class="details__spec-label">Modelo</div>
                  <div class="details__spec-value"><?= h($anuncio['modelo']) ?></div>
                </div>
                <div class="details__spec">
                  <div class="details__spec-label">Ano</div>
                  <div class="details__spec-value"><?= (int)$anuncio['ano_fabricacao'] ?></div>
                </div>
                <div class="details__spec">
                  <div class="details__spec-label">Cor</div>
                  <div class="details__spec-value"><?= h($anuncio['cor']) ?></div>
                </div>
                <div class="details__spec">
                  <div class="details__spec-label">Quilometragem</div>
                  <div class="details__spec-value"><?= number_format((float)$anuncio['quilometragem'], 0, ',', '.') ?> km</div>
                </div>
                <div class="details__spec">
                  <div class="details__spec-label">Localização</div>
                  <div class="details__spec-value"><?= h($anuncio['cidade'] . ' - ' . $anuncio['estado']) ?></div>
                </div>
              </div>

              <div class="details__description">
                <h3>Descrição do Anunciante</h3>
                <p><?= nl2br(h($anuncio['descricao'])) ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    &copy; 2026 veloCity — Todos os direitos reservados.
  </footer>
</body>
</html>
