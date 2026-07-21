<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

iniciar_sessao();

if (!usuario_logado()) {
    json_response(false, 'É necessário estar logado.', [], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Método não permitido.', [], 405);
}

exigir_csrf($_POST['csrf_token'] ?? null);

$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$ano = (int)($_POST['ano'] ?? 0);
$cor = trim($_POST['cor'] ?? '');
$km = (int)($_POST['km'] ?? -1);
$valor = (float)str_replace(',', '.', (string)($_POST['valor'] ?? '-1'));
$estado = strtoupper(trim($_POST['estado'] ?? ''));
$cidade = trim($_POST['cidade'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

$erros = [];
if ($marca === '') $erros[] = 'Marca é obrigatória.';
if ($modelo === '') $erros[] = 'Modelo é obrigatório.';
if ($ano < 1900 || $ano > (int)date('Y') + 1) $erros[] = 'Ano de fabricação inválido.';
if ($cor === '') $erros[] = 'Cor é obrigatória.';
if ($km < 0) $erros[] = 'Quilometragem inválida.';
if ($valor <= 0) $erros[] = 'Valor inválido.';
if (!preg_match('/^[A-Z]{2}$/', $estado)) $erros[] = 'Estado inválido.';
if ($cidade === '') $erros[] = 'Cidade é obrigatória.';
if (mb_strlen($descricao) < 10) $erros[] = 'Descrição muito curta.';

$fotos = $_FILES['fotos'] ?? null;
$totalFotos = $fotos ? count(array_filter($fotos['name'])) : 0;
if ($totalFotos < 3) {
    $erros[] = 'Envie pelo menos 3 fotos do veículo.';
}

if ($erros) {
    json_response(false, implode(' ', $erros), [], 422);
}

$tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$diretorioBase = __DIR__ . '/../uploads/anuncios/';
if (!is_dir($diretorioBase)) {
    mkdir($diretorioBase, 0755, true);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$caminhosSalvos = [];

for ($i = 0; $i < count($fotos['name']); $i++) {
    if ($fotos['error'][$i] === UPLOAD_ERR_NO_FILE) {
        continue;
    }
    if ($fotos['error'][$i] !== UPLOAD_ERR_OK) {
        json_response(false, 'Falha no envio de uma das fotos.', [], 422);
    }
    if ($fotos['size'][$i] > 5 * 1024 * 1024) {
        json_response(false, 'Cada foto deve ter no máximo 5MB.', [], 422);
    }

    $tipoReal = $finfo->file($fotos['tmp_name'][$i]);
    if (!isset($tiposPermitidos[$tipoReal])) {
        json_response(false, 'Apenas imagens JPEG, PNG ou WEBP são permitidas.', [], 422);
    }

    // Nome aleatório: evita colisões e neutraliza qualquer nome/extensão
    // enviado pelo cliente (o tipo real do arquivo já foi checado acima).
    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$tipoReal];

    if (!move_uploaded_file($fotos['tmp_name'][$i], $diretorioBase . $nomeArquivo)) {
        json_response(false, 'Falha ao salvar uma das fotos.', [], 500);
    }

    $caminhosSalvos[] = 'backend/uploads/anuncios/' . $nomeArquivo;
}

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare(
        'INSERT INTO anuncios (usuario_id, marca, modelo, ano_fabricacao, cor, quilometragem, valor, estado, cidade, descricao)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$_SESSION['usuario_id'], $marca, $modelo, $ano, $cor, $km, $valor, $estado, $cidade, $descricao]);
    $anuncioId = (int)$pdo->lastInsertId();

    $stmtFoto = $pdo->prepare('INSERT INTO anuncio_fotos (anuncio_id, caminho) VALUES (?, ?)');
    foreach ($caminhosSalvos as $caminho) {
        $stmtFoto->execute([$anuncioId, $caminho]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    foreach ($caminhosSalvos as $caminho) {
        $arquivo = __DIR__ . '/../../' . $caminho;
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
    json_response(false, 'Erro ao salvar o anúncio.', [], 500);
}

json_response(true, 'Anúncio criado com sucesso!', ['redirect' => 'meus-anuncios.php']);
