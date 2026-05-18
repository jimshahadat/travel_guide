<?php
$base = '/travel_guide';
$cur  = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
  <a href="<?= $base ?>/index.php" class="navbar-brand">
    🌍 Wander<span>lust</span>
  </a>
  <div class="navbar-links">
    <?php if (isLoggedIn()): ?>
      <span class="navbar-user">
        <?= e($_SESSION['name']) ?>
        <span class="badge-role"><?= e($_SESSION['role']) ?></span>
      </span>

      <?php if (isVerified()): ?>
        <a href="<?= $base ?>/index.php" <?= $cur==='index.php'?'class="active"':'' ?>>Home</a>

        <?php if ($_SESSION['role']==='user'): ?>
          <a href="<?= $base ?>/wishlist.php" <?= $cur==='wishlist.php'?'class="active"':'' ?>>♡ Wishlist</a>
        <?php endif; ?>

        <?php if ($_SESSION['role']==='scout'): ?>
          <a href="<?= $base ?>/scout/requests.php" <?= $cur==='requests.php'?'class="active"':'' ?>>My Requests</a>
          <a href="<?= $base ?>/scout/create.php"   <?= $cur==='create.php'  ?'class="active"':'' ?>>+ New Request</a>
          <a href="<?= $base ?>/scout/approved.php" <?= $cur==='approved.php'?'class="active"':'' ?>>Approved Posts</a>
        <?php endif; ?>

        <?php if ($_SESSION['role']==='admin'): ?>
          <a href="<?= $base ?>/admin/dashboard.php">Admin Dashboard</a>
        <?php endif; ?>
      <?php endif; ?>

      <a href="<?= $base ?>/profile.php" <?= $cur==='profile.php'?'class="active"':'' ?>>Profile</a>
      <a href="<?= $base ?>/logout.php" class="btn-logout">Logout</a>

    <?php else: ?>
      <a href="<?= $base ?>/login.php"    <?= $cur==='login.php'   ?'class="active"':'' ?>>Login</a>
      <a href="<?= $base ?>/register.php" <?= $cur==='register.php'?'class="active"':'' ?>>Register</a>
    <?php endif; ?>
  </div>
</nav>
