<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../controllers/AdminController.php';
AdminController::editPost();
