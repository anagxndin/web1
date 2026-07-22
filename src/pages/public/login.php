<?php
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/csrf.php';
require_once __DIR__ . '/../../../backend/includes/functions.php';

iniciar_sessao();

if (usuario_logado()) {
    header('Location: ../area-restrita/principalRestrita.php');
    exit;
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — veloCity</title>
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
        <span id="navAuth"><a href="cadastro.php" class="navbar__cta">Cadastre-se</a></span>
      </nav>
    </div>
  </header>

  <main class="page" id="main-content">
    <div class="auth">
      <div class="auth__card">
        <div class="auth__header">
          <div class="heading-2">Entrar</div>
          <p>Acesse sua conta para gerenciar seus anúncios</p>
        </div>

        <div id="formAlert" class="alert alert--hidden" role="alert"></div>

        <form id="formLogin" class="auth__form" action="../../../backend/api/login.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
          <div class="form-group">
            <label for="loginEmail" class="form-label">
              <i class="bi bi-envelope"></i> E-mail
            </label>
            <input type="email" id="loginEmail" name="email" class="form-input" placeholder="seu.email@exemplo.com" required>
          </div>

          <div class="form-group">
            <label for="loginSenha" class="form-label">
              <i class="bi bi-lock"></i> Senha
            </label>
            <input type="password" id="loginSenha" name="senha" class="form-input" placeholder="Digite sua senha" required>
          </div>

          <button type="submit" class="btn btn--primary btn--full">
            <i class="bi bi-box-arrow-in-right"></i> Entrar
          </button>
        </form>

        <div class="auth__footer">
          Não possui conta? <a href="cadastro.php">Cadastre-se aqui</a>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    &copy; 2026 veloCity — Todos os direitos reservados.
  </footer>
</body>
</html>
