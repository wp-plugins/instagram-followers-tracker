<?php
/**
 * @package Zebra_Boss
 * @version 0.2
 */
/*
Plugin Name: Instagram Followers Tracking Widget
Plugin URI: http://zebraboss.com/wordpress
Description: ZebraBoss Instagram Followers Tracking Widget allows you to track your ZebraBoss statistics for your Instagramm account right from the Wordpress Admin Panel.
Author: Mike Tishetsky
Version: 0.2
Author URI: http://vk.com/tishetsky
*/

	define('ACCOUNT_ID_NAME', 'ift_account_id');
	define('TEXT_DOMAIN', 'zebraboss');

	switch (WPLANG) {
		case 'ru':
		case 'ru_RU':
		case 'ru_UA':
			$_data_host_ = "ru.zebraboss.com";
			$_utm_       = "utm_source=instfollowtracker&utm_medium=wordpressru&utm_campaign=apps";
			$_utm_subs_   = "utm_source=instfollowtracker&utm_medium=wordpressrusubs&utm_campaign=apps";
		break;
		default:
			$_data_host_ = "www.zebraboss.com";
			$_utm_       = "utm_source=instfollowtracker&utm_medium=wordpressen&utm_campaign=apps";
			$_utm_subs_   = "utm_source=instfollowtracker&utm_medium=wordpressensubs&utm_campaign=apps";
	}

	load_plugin_textdomain( TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)).'/languages/');

	function ift_widget_menu() {
		add_options_page( __('Instagram Followers Tracking options', TEXT_DOMAIN), __('IFT Settings', TEXT_DOMAIN), 'manage_options', 'ift_settings', 'ift_settings');
	}

	add_action( 'admin_menu', 'ift_widget_menu' );

	function ift_settings() {
		if (! current_user_can( 'manage_options' )) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include dirname(__FILE__).'/options.php';
	}

	function load_data() {

		if (! function_exists('json_decode')) {
			wp_die(__('JSON extension is required to operate this plugin'));
		}

		$source = get_option(ACCOUNT_ID_NAME);
		if (strpos($source, '@') !== false) {
			$source_type = 'email';
			$source = "http://{$GLOBALS['_data_host_']}/reports/byemail.json?email={$source}";
		}
		else {
			$source_type = 'url';
			$source = preg_replace('!\.html$!', '.json', $source);
			$source .= "?".rand(1111,9999);
		}

		$data = false;
		$data = file_get_contents($source);
		if (!$data) {
			$data = __("Loading data failed");
		}
		else {
			$data = json_decode($data);
//			print "<pre>";print_r($data);
		}

		return $data;
	}

	function show_stats() {
		$data = load_data();

		$acc_name    = __( 'Account name', TEXT_DOMAIN );
		$acc_total   = __( 'Followers', TEXT_DOMAIN );
		$acc_added   = __( 'Subscribed', TEXT_DOMAIN );
		$acc_removed = __( 'Unsubscribed', TEXT_DOMAIN );

		$x = <<<ZZZ
		<style>
			table#ift td {text-align:right}
			td.left {text-align:left!important}
		</style>
		<table id="ift">
        	<tr>
        		<td class="left">{$acc_name}</td>
        		<td>{$acc_total}</td>
        		<td>{$acc_added}</td>
        		<td>{$acc_removed}</td>
        	</tr>
ZZZ;
		foreach ($data->accounts as $id => $acc) {
			if (!isset($report_hash)) {
				$report_hash = $acc->report_hash;
			}
			$x .= <<<ZZZ
			<tr>
				<td class="left"><a href="{$acc->url}">{$acc->name}</a></td>
				<td>{$acc->followers_count}</td>
				<td>+{$acc->added}</td>
				<td>-{$acc->removed}</td>
			</tr>
ZZZ;
		}

		$view_full_report = __( 'View full report', TEXT_DOMAIN );

		$x .= <<<ZZZ
			</table>
			<b><a href="http://{$GLOBALS['_data_host_']}/reports/{$report_hash}.html?{$GLOBALS['_utm_']}">{$view_full_report}</a></b>
ZZZ;

		print $x;
	}

//	add_action( 'admin_notices', 'show_stats' );

	function dashboard_show_stats($post, $callback) {
		show_stats();
	}

	function add_ift_widget() {
		wp_add_dashboard_widget('ift_dashboard_widget', 'Instagram Followers Tracker', 'dashboard_show_stats');
	}

	add_action('wp_dashboard_setup', 'add_ift_widget');

	function dolly_css() {

		$style = <<<ZZZ
		<style type='text/css'>
		#dolly {
			padding:5px;
			display:block;
			text-align:center;
		}
		</style>
ZZZ;
		print $style;

	}
	add_action( 'admin_head', 'dolly_css' );

?>
