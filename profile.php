<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/ProfileController.php';
if ($_SERVER['REQUEST_METHOD']==='POST') ProfileController::handleUpdate();
else ProfileController::show();
