<?
	$option_name = 'ift_value';
	delete_option($option_name);
	if (isset($_POST['X']) && $_POST['X'] == 'X') {
		// save post
		update_option(ACCOUNT_ID_NAME, $_POST['id']);
		$ok = true;
	}

?>

<?php if ($ok): ?>
	<div class="updated"><p><strong><?php _e( 'Settings saved.', TEXT_DOMAIN ); ?></strong></p></div>
<?php endif ?>

	<div class="wrap">
		<h2><?php _e( 'Instagram Followers Tracker Settings', TEXT_DOMAIN ) ?></h2>
		<form method="post" action="">
			<input type="hidden" name="X" value="X" />
			<?php _e( 'Enter your email used to subscribe to Zebraboss reports or the Zebraboss report URL', TEXT_DOMAIN ) ?>
			<br />
			<input type="text" size="100" name="id" value="<?= get_option(ACCOUNT_ID_NAME) ?>" />
			<br />
			<?php _e( 'Not subsribed to reports?', TEXT_DOMAIN ) ?> 
			<a href="http://<?php echo $GLOBALS['_data_host_'] ?>/?<?php echo $GLOBALS['_utm_subs_'] ?>"><?php _e( 'Subscribe here!', TEXT_DOMAIN ) ?></a>
			<br />
			<input type="submit" name="submit" value="<?php _e( 'Save settings', TEXT_DOMAIN ) ?>" />
		</form>
	</div>
