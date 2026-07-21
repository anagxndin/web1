<?php
require __DIR__ . '/../../../backend/includes/guard.php';
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Anúncio - veloCity</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/estilos.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <script src="../../scripts/scripts.js"></script>
    <script src="../../scripts/forms.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
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
                    <li class="nav-item"><a class="nav-link active" href="criar-anuncio.php">Novo Anúncio</a></li>
                    <li class="nav-item"><a class="nav-link" href="meus-anuncios.php">Meus Anúncios</a></li>
                </ul>
                <a class="btn btn-outline-light btn-sm" href="../../../backend/api/logout.php">Sair / Logoff</a>
            </div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <div id="formAlert" class="alert d-none" role="alert"></div>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h3 class="mb-0" style="color: var(--azul-escuro);">Criar Novo Anúncio</h3>
            </div>
            <div class="card-body p-4">
                <form class="row g-3" id="formCriarAnuncio" action="../../../backend/api/anuncio_criar.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                    <div class="col-md-4">
                        <label for="marca" class="form-label">Marca</label>
                        <select class="form-select" id="marca" name="marca" required>
                            <option value="">Selecione...</option>
                            <option value="Fiat">Fiat</option>
                            <option value="Volkswagen">Volkswagen</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="Ford">Ford</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="BYD">BYD</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="col-md-4">
                        <label for="ano" class="form-label">Ano de Fabricação</label>
                        <input type="number" class="form-control" id="ano" name="ano" min="1900" max="2027" required>
                    </div>
                    <div class="col-md-3">
                        <label for="cor" class="form-label">Cor</label>
                        <input type="text" class="form-control" id="cor" name="cor" required>
                    </div>
                    <div class="col-md-3">
                        <label for="km" class="form-label">Quilometragem</label>
                        <input type="number" class="form-control" id="km" name="km" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label for="valor" class="form-label">Valor (R$)</label>
                        <input type="number" class="form-control" id="valor" name="valor" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="">Selecione...</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="SP">São Paulo</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="BA">Bahia</option>
                            <option value="PR">Paraná</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" required>
                    </div>
                    <div class="col-md-12">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="fotos" class="form-label">Fotos (Selecione pelo menos 3)</label>
                        <input class="form-control" type="file" id="fotos" name="fotos[]" multiple accept="image/png, image/jpeg, image/webp" required>
                        <small class="text-muted">Pressione CTRL para selecionar múltiplos arquivos. Máx. 5MB por foto.</small>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-velocity-primary px-4">Salvar Anúncio</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-velocity-footer text-white text-center p-3 mt-auto">
        &copy; 2026 veloCity - Todos os direitos reservados.
    </footer>
</body>
</html>
