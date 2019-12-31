<header>
	<nav>
		<ul>
			<li><a href="<?php echo SITE_ROOT; ?>/index.php">Home</a></li>
			<li><a href="<?php echo SITE_ROOT; ?>/downloads.php">Downloads</a></li>
			<li><a href="<?php echo SITE_ROOT; ?>/forum.php">Forum</a></li>
			<li><a href="<?php echo SITE_ROOT; ?>/contact.php">Contact us</a></li>
		<?php if (isset($_SESSION['user_id']) && $user_data['is_admin'] == 1): ?>
			<li><a href="<?php echo SITE_ROOT; ?>/emailusers.php">Mail</a><li>
		<?php endif; ?>
		</ul>
	</nav>
	<div class="clear"></div>
</header>