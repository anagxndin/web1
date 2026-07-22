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
    header('Location: index.php');
    exit;
}

$fotoUrl = $anuncio['foto'] ? '../../../' . $anuncio['foto'] : '../../assets/images/carLogo.png';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Interesse — veloCity</title>
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

      <a href="index.php" class="navbar__brand"><em>veloCity</em></a>

      <nav class="navbar__links" id="dropdownMenu">
        <a href="index.php" class="navbar__link">Home</a>
        <a href="../area-restrita/principalRestrita.php" class="navbar__link">Home | Vendedor (Restrita a usuários)</a>
        <span id="navAuth"><a href="login.php" class="navbar__cta">Entrar</a></span>
      </nav>
    </div>
  </header>

  <main class="page page__main--sm" id="main-content">
    <div class="container" style="max-width: 800px;">
      <div class="section__title">
        <div class="heading-2">Tenho Interesse!</div>
        <p>Envie sua mensagem para o anunciante</p>
      </div>

      <div id="formAlert" class="alert alert--hidden" role="alert"></div>

      <div class="card" style="margin-bottom: 2rem;">
        <div style="display:flex;flex-wrap:wrap;gap:1.5rem;padding:1.5rem;">
          <div style="flex:1;min-width:250px;">
            <img src="<?= h($fotoUrl) ?>" alt="<?= h($anuncio['marca'] . ' ' . $anuncio['modelo']) ?>" style="width:100%;height:250px;object-fit:cover;border-radius:var(--radius-lg);">
          </div>
          <div style="flex:1;min-width:250px;">
            <div class="card__header" style="margin-bottom:0.75rem;">
              <h3 class="card__title" style="font-size:1.25rem;"><?= h($anuncio['marca'] . ' ' . $anuncio['modelo']) ?></h3>
              <span class="card__badge"><?= (int)$anuncio['ano_fabricacao'] ?></span>
            </div>
            <p class="card__meta"><i class="bi bi-geo-alt"></i> <?= h($anuncio['cidade'] . ', ' . $anuncio['estado']) ?></p>
            <p class="card__price" style="font-size:1.5rem;">R$ <?= number_format((float)$anuncio['valor'], 2, ',', '.') ?></p>
            <p class="text-muted" style="margin-top:0.75rem;"><?= nl2br(h($anuncio['descricao'])) ?></p>
          </div>
        </div>
      </div>

      <div class="auth__card" style="max-width:100%;">
        <form id="formInteresse" class="auth__form" action="../../../backend/api/interesse_criar.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
          <input type="hidden" name="anuncio_id" value="<?= (int)$anuncio['id'] ?>">

          <div class="form-group">
            <label for="interesseNome" class="form-label">
              <i class="bi bi-person"></i> Nome Completo
            </label>
            <input type="text" id="interesseNome" name="nome" class="form-input" placeholder="Digite seu nome completo" required>
          </div>

          <div class="form-group">
            <label for="interesseTelefone" class="form-label">
              <i class="bi bi-telephone"></i> Telefone
            </label>
            <input type="tel" id="interesseTelefone" name="telefone" class="form-input" placeholder="(11) 99999-9999" required>
          </div>

          <div class="form-group">
            <label for="interesseMensagem" class="form-label">
              <i class="bi bi-chat-dots"></i> Mensagem
            </label>
            <textarea id="interesseMensagem" name="mensagem" class="form-textarea" placeholder="Deixe sua mensagem de interesse aqui..." rows="5" required></textarea>
          </div>

          <button type="submit" class="btn btn--primary btn--full">
            <i class="bi bi-send"></i> Enviar Interesse
          </button>
        </form>
      </div>
    </div>
  </main>

  <footer class="footer">
    &copy; 2026 veloCity — Todos os direitos reservados.
  </footer>
</body>
</html>
