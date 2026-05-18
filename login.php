<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/controllers/AuthController.php';
if ($_SERVER['REQUEST_METHOD']==='POST') AuthController::handleLogin();
else AuthController::showLogin();
