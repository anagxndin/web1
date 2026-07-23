<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Anuncio.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Método não permitido.', [], 405);
}

$filtros = [];
foreach (['marca', 'modelo', 'cidade'] as $campo) {
    if (!empty($_GET[$campo])) {
        $filtros[$campo] = $_GET[$campo];
    }
}

$anuncios = Anuncio::listar($filtros);

json_response(true, 'ok', ['anuncios' => $anuncios]);
