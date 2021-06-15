<?php
/** @var array $links */
?>

<nav class="navbar">
	<div class="container">
		<div class="navbar-menu"><?php
		foreach ($links as $index => $link) {
			wl('<a class="navbar-item" href="' . $link['u'] . '">' . $link['t'] . '</a>');
		} ?>
		</div>
	</div>
</nav>
