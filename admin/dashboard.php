<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/PostRequest.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../controllers/AdminController.php';
AdminController::dashboard();
