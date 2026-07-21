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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="../../styles/estilos.css">
    <link rel="stylesheet" href="../../styles/login.css">
    <link rel="stylesheet" href="../../styles/style.css">

    <script src="../../scripts/scripts.js"></script>
    <script src="../../scripts/forms.js"></script>
</head>
<body class="pageWrapper">
    <header class="mainHeader">
      <button id="dropdownToggle"><i class="bi bi-list" id="menuIcon"></i></button>
      <div id="logo">
        <h1><i>veloCity</i></h1>
      </div>
      <nav class="dropdownMenu" id="dropdownMenu">
        <a href="index.html" class="dropdownItem">Home</a>
        <a href="../area-restrita/principalRestrita.php" class="dropdownItem">Home | Vendedor (Restrita a usuários)</a>
      </nav>

      <img src="../../assets/images/carLogo.png" alt="Logo" class="logoCar"/>
    </header>

    <nav class="publicNavBar">
        <a href="login.php" class="publicNavItem">Login</a>
        <a href="cadastro.php" class="publicNavItem">Cadastro</a>
    </nav>

    <main class="mainContent">
    <div class="loginContainer">
        <div class="loginForm">
            <div class="loginHeader">
                <i class="bi bi-unlock loginIcon"></i>
                <h2>Login</h2>
            </div>
            <p class="loginSubtitle">Acesse sua conta para gerenciar seus anúncios</p>

            <div id="formAlert" class="formAlert" style="display:none;"></div>

            <form id="formLogin" action="../../../backend/api/login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                <div class="formGroup">
                    <label for="loginEmail">
                        <i class="bi bi-envelope"></i> E-mail
                    </label>
                    <input
                        type="email"
                        id="loginEmail"
                        name="email"
                        placeholder="seu.email@exemplo.com"
                        required
                    >
                </div>

                <div class="formGroup">
                    <label for="loginSenha">
                        <i class="bi bi-lock"></i> Senha
                    </label>
                    <input
                        type="password"
                        id="loginSenha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >
                </div>

                <button type="submit" class="btnLogin">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </button>
            </form>

            <div class="loginDivider"></div>

            <p class="loginCadastro">
                Não possui conta? <a href="cadastro.php">Cadastre-se aqui</a>
            </p>
        </div>
    </div>
    </main>

    <footer class="bg-velocity-footer simpleFooter">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>
</body>
</html>
