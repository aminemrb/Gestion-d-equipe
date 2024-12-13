<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../app/config.php';

$router = require __DIR__ . '/../routes/routes.php';

// Récupère l'URI de la requête
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dispatch la requête vers le contrôleur et l'action appropriés
$router->dispatch($uri);

header('Location: ' . BASE_URL . '/vues/Accueil/accueil.php');
exit;