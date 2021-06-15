<?php

/** @var string $pageTitle */

// HTML head
require_once __DIR__ . '/../../layout/header/head.php'; ?>

<body>
<?php

	// Top navigation
	require_once __DIR__ . '/../../layout/header/top-nav.php'; ?>

	<div class="container">

		<h1 class="title is-4">
			<?= $pageTitle ?>
		</h1>

		<script src="/s/j/ext/vue.global.js"></script>

		<script>
			<?php require_once PRJ_ROOT . '/www/s/j/vue/state.js'; ?>
		</script>
		<?php
			require_once PRJ_ROOT . '/www/s/j/vue/logRow.vue';
			require_once PRJ_ROOT . '/www/s/j/vue/logPanel.vue';
			require_once PRJ_ROOT . '/www/s/j/vue/app.vue';
		?>
	</div>

	<?php // Vue ?>
</body>

<?php
// Footer
require_once __DIR__ . '/../../layout/footer.php';

