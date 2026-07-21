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
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="../../styles/estilos.css">
    <link rel="stylesheet" href="../../styles/cadastro.css">
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
        <a href="../area-restrita/principalRestrita.php" class="dropdownItem">Home do Vendedor | (Restrita a usuários)</a>
      </nav>

      <img src="../../assets/images/carLogo.png" alt="Logo" class="logoCar"/>
    </header>

    <nav class="publicNavBar">
        <a href="login.php" class="publicNavItem">Login</a>
        <a href="cadastro.php" class="publicNavItem">Cadastro</a>
    </nav>

    <main class="mainContent">
    <div class="cadastroContainer">
        <div class="cadastroForm">
            <h2>Cadastro de Usuário</h2>
            <p class="cadastroSubtitle">Crie sua conta para anunciar seus veículos</p>

            <div id="formAlert" class="formAlert" style="display:none;"></div>

            <form id="formCadastro" action="../../../backend/api/cadastro.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                <div class="formGroup">
                    <label for="nome">
                        <i class="bi bi-person"></i> Nome Completo
                    </label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Digite seu nome completo"
                        required
                    >
                </div>

                <div class="formGroup">
                    <label for="cpf">
                        <i class="bi bi-person-vcard"></i> CPF
                    </label>
                    <input
                        type="text"
                        id="cpf"
                        name="cpf"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        required
                    >
                </div>

                <div class="formGroup">
                    <label for="email">
                        <i class="bi bi-envelope"></i> E-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="seu.email@exemplo.com"
                        required
                    >
                </div>

                <div class="formGroup">
                    <label for="telefone">
                        <i class="bi bi-telephone"></i> Telefone
                    </label>
                    <input
                        type="tel"
                        id="telefone"
                        name="telefone"
                        placeholder="(11) 99999-9999"
                        required
                    >
                </div>

                <div class="formGroup">
                    <label for="senha">
                        <i class="bi bi-lock"></i> Senha
                    </label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite uma senha forte (mín. 8 caracteres)"
                        minlength="8"
                        required
                    >
                </div>

                <button type="submit" class="btnCadastro">
                    <i class="bi bi-person-plus"></i> Cadastrar
                </button>
            </form>

            <p class="cadastroLink">
                Já possui conta? <a href="login.php">Faça login aqui</a>
            </p>
        </div>
    </div>
    </main>

    <footer class="bg-velocity-footer simpleFooter">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>
</body>
</html>
