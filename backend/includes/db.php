<?php
require_once __DIR__ . '/../models/Database.php';

/**
 * Função para compatibilidade com código legado que chama db().
 */
function db(): PDO
{
    return Database::conectar();
}
