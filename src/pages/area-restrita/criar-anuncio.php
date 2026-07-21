<?php
require __DIR__ . '/../../../backend/includes/guard.php';
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Novo Anúncio — veloCity</title>
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
        <a href="criar-anuncio.php" class="navbar__link navbar__link--active">Anunciar</a>
        <a href="meus-anuncios.php" class="navbar__link">Meus Anúncios</a>
        <a href="principalRestrita.php" class="navbar__link">Painel</a>
      </nav>
    </div>
  </header>

  <div class="toolbar">
    <div class="toolbar__inner">
      <nav class="toolbar__links">
        <a href="criar-anuncio.php" class="toolbar__link toolbar__link--active">Novo Anúncio</a>
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
      <div class="page-header">
        <h2 class="page-header__title">Criar Novo Anúncio</h2>
      </div>

      <div id="formAlert" class="alert alert--hidden" role="alert"></div>

      <div class="card">
        <div class="card__body">
          <form id="formCriarAnuncio" action="../../../backend/api/anuncio_criar.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;margin-bottom:1.5rem;">
              <div class="form-group">
                <label for="marca" class="form-label">Marca</label>
                <select id="marca" name="marca" class="form-select" required>
                  <option value="">Selecione</option>
                  <option value="Fiat">Fiat</option>
                  <option value="Volkswagen">Volkswagen</option>
                  <option value="Chevrolet">Chevrolet</option>
                  <option value="Ford">Ford</option>
                  <option value="Toyota">Toyota</option>
                  <option value="Hyundai">Hyundai</option>
                  <option value="BYD">BYD</option>
                </select>
              </div>

              <div class="form-group">
                <label for="modelo" class="form-label">Modelo</label>
                <input type="text" id="modelo" name="modelo" class="form-input" placeholder="Ex: Corolla" required>
              </div>

              <div class="form-group">
                <label for="ano" class="form-label">Ano</label>
                <input type="number" id="ano" name="ano" class="form-input" min="1900" max="2027" placeholder="2022" required>
              </div>

              <div class="form-group">
                <label for="cor" class="form-label">Cor</label>
                <input type="text" id="cor" name="cor" class="form-input" placeholder="Ex: Prata" required>
              </div>

              <div class="form-group">
                <label for="km" class="form-label">Quilometragem</label>
                <input type="number" id="km" name="km" class="form-input" min="0" placeholder="45000" required>
              </div>

              <div class="form-group">
                <label for="valor" class="form-label">Valor (R$)</label>
                <input type="number" id="valor" name="valor" class="form-input" min="0" step="0.01" placeholder="85000" required>
              </div>

              <div class="form-group">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select" required>
                  <option value="">Selecione</option>
                  <option value="MG">Minas Gerais</option>
                  <option value="SP">São Paulo</option>
                  <option value="RJ">Rio de Janeiro</option>
                  <option value="DF">Distrito Federal</option>
                  <option value="BA">Bahia</option>
                  <option value="PR">Paraná</option>
                </select>
              </div>

              <div class="form-group">
                <label for="cidade" class="form-label">Cidade</label>
                <input type="text" id="cidade" name="cidade" class="form-input" placeholder="Ex: Uberlândia" required>
              </div>
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
              <label for="descricao" class="form-label">Descrição</label>
              <textarea id="descricao" name="descricao" class="form-textarea" rows="4" placeholder="Descreva o estado do veículo, opcionais, histórico..." required></textarea>
            </div>

            <div class="form-group" style="margin-bottom:2rem;">
              <label class="form-label">Fotos (mínimo 3)</label>
              <div class="upload-area">
                <input type="file" id="fotos" name="fotos[]" class="form-file__input" multiple accept="image/png, image/jpeg, image/webp" required>
                <div class="upload-area__icon"><i class="bi bi-camera"></i></div>
                <div class="upload-area__text">Clique para selecionar as fotos do veículo</div>
                <div class="upload-area__hint">PNG, JPEG ou WebP. Máx. 5MB por foto. Selecione pelo menos 3.</div>
              </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--lg">
              <i class="bi bi-check-lg"></i> Publicar Anúncio
            </button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    &copy; 2026 veloCity — Todos os direitos reservados.
  </footer>
</body>
</html>
