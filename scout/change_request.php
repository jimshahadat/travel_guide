<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/ScoutController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') ScoutController::handleChangeRequest();
else ScoutController::changeRequestForm();
