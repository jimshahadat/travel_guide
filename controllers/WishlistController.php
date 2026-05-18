<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Wishlist.php';

class WishlistController {
    public static function show(): void {
        requireLogin();
        requireVerified();
        requireRole('user');
        $items = Wishlist::getByUser((int)$_SESSION['user_id']);
        require __DIR__ . '/../views/wishlist/wishlist.php';
    }
}
