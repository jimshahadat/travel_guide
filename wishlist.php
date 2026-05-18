<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Wishlist.php';
require_once __DIR__ . '/controllers/WishlistController.php';
WishlistController::show();
