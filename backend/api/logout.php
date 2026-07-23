<?php
require_once __DIR__ . '/../includes/auth.php';

iniciar_sessao();
logout_usuario();

header('Location: ../../src/pages/public/login.php');
exit;
