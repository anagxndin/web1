<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Método não permitido.', [], 405);
}

$pdo = db();
$condicoes = [];
$parametros = [];

foreach (['marca', 'modelo', 'cidade'] as $campo) {
    if (!empty($_GET[$campo])) {
        $condicoes[] = "a.$campo LIKE ?";
        $parametros[] = '%' . $_GET[$campo] . '%';
    }
}

$sql = "SELECT a.id, a.marca, a.modelo, a.ano_fabricacao, a.cidade, a.estado, a.valor,
               (SELECT f.caminho FROM anuncio_fotos f WHERE f.anuncio_id = a.id ORDER BY f.id ASC LIMIT 1) AS foto
        FROM anuncios a";

if ($condicoes) {
    $sql .= ' WHERE ' . implode(' AND ', $condicoes);
}

$sql .= ' ORDER BY a.criado_em DESC LIMIT 100';

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);

json_response(true, 'ok', ['anuncios' => $stmt->fetchAll()]);
