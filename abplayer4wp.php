<?php
/**
 * Plugin Name: ABPlayerHTML5 for WordPess
 * Plugin URI: http://kanoha.org/tags/abplayer4wp
 * Description: Allows you to embed a danmaku player to your wordpress site.
 * Version: 0.8
 * Author: Jim Chen
 * Author URI: http://kanoha.org/
 * License: MIT
 */

wp_register_style("abpwp-base-css", plugins_url('/assets/base.css', __FILE__));
register_activation_hook(__FILE__, "abpwp_create_db");

add_action('wp_enqueue_scripts', 'abpwp_add_styles');
add_action('wp_enqueue_scripts', 'abpwp_add_scripts');

function abpwp_add_styles(){
	wp_enqueue_style('abpwp-base-css', plugins_url('/assets/base.css', __FILE__));
}

function abpwp_add_scripts(){
	wp_enqueue_script('abpwp-js-commentcore', plugins_url('/assets/CommentCoreLibrary.js', __FILE__), false );
	wp_enqueue_script('abpwp-js-requester', plugins_url('/assets/libxml.js', __FILE__), false );
	wp_enqueue_script('abpwp-js-send', plugins_url('/assets/sendDispatcher.js', __FILE__), false );
	wp_enqueue_script('abpwp-js-player', plugins_url('/assets/player.js', __FILE__), false );
	wp_localize_script('abpwp-js-requester', 'ajaxurl', admin_url( 'admin-ajax.php') );
}

function abpwp_create_db(){
	global $wpdb;
	$QUERY = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "danmaku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pool` text NOT NULL,
  `text` text NOT NULL,
  `type` int(11) NOT NULL,
  `stime` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `color` int(11) NOT NULL,
  `author` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
	$wpdb->query($QUERY);
}

add_shortcode('danmaku', 'register_abpwpshortcode');

function abpwp_danmaku_id($idbase){
	$id = get_option("danmaku_id_template", "abp{pid}");
	$id = preg_replace("~\{pid\}~iUs", $idbase, $id);
	return $id;
}

function abpwp_video_to_source($video){
	$v = "";
	foreach($video as $type => $url){
		$v.='<source src="' . $url .'" type="video/' . $type . '">';
	}
	return $v;
}

function register_abpwpshortcode($atts){
	if(!isset($atts['src'])){
		return "<strong>Error: SRC parameter not given in shortcode!</strong>";
	}
	// Get the danmaku id
	$dmid = isset($atts['id']) ? $atts['id'] : abpwp_danmaku_id(get_the_id());
	$formats = array();
	$default = 'mp4';
	// Guess the format of the source
	if(isset($atts['src'])){
		$extension = strtolower(pathinfo($atts['src'], PATHINFO_EXTENSION));
		if(!in_array($extension, array("mp4","webm","ogv"))){
			$extension = "none";
		}
		$formats[$extension] = $atts['src'];
	}
	// Get video formats
	if(isset($atts['mp4'])){
		$formats['mp4'] = $atts['mp4'];
	}
	if(isset($atts['webm'])){
		$formats['webm'] = $atts['webm'];
	}
	if(isset($atts['ogv'])){
		$formats['ogv'] = $atts['ogv'];
	}
	$body = '<div id="player-' . $dmid . '" tabindex="1"></div>
			<video id="abp-video-' . $dmid . '" autobuffer="true" data-setup="{}">'. abpwp_video_to_source($formats) .'</video>';
	$init = '<script type="text/javascript">
			window.addEventListener("load",function(){
				var inst = ABP.create(document.getElementById("player-' . $dmid . '"),{
					"src":document.getElementById("abp-video-' . $dmid .'"),
					"width":640,
					"height":480
				});
				inst.state.allowRescale = true;
				WPCommentLoader("' . $dmid .'", inst.cmManager);
				inst.remote = new CommentSendContract();
				inst.txtText.focus();
				bindABPlayerInstance(inst);
				window.abpinst = inst;
			});
		</script>';
	return $body . $init;
}

add_action('wp_ajax_danmaku', 'abpwp_ajax_danmaku' );
add_action('wp_ajax_nopriv_danmaku', 'abpwp_ajax_danmaku' );

function abpwp_ajax_danmaku(){
	global $wpdb;
	$danmakus = Array();
	$q = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "danmaku` WHERE `pool` = \"" . 
		mysql_real_escape_string($_POST['id']) . "\" ORDER BY `id` DESC LIMIT 0," . 
		((int) get_option("danmaku_pool_limit", "4000")) . ";");
	foreach($q as $line){
		$danmakus[] = Array(
			"stime"=> (int)$line->stime,
			"text"=> $line->text,
			"mode"=> (int)$line->type,
			"color"=> "#" . substr("000000".dechex((int)$line->color),-6),
			"dbid"=> (int)$line->id,
			"date"=> $line->date
		);
	}
	echo json_encode(Array(
		"v" => 1,
		"len" => count($danmakus),
		"timeline" => $danmakus,
	));
	die();
};

add_action('admin_menu', 'abpwp_create_menu');

function abpwp_create_menu() {
	add_menu_page(__('Danmaku Settings',"abpwp"), __('Danmaku',"abpwp"), 'administrator', "abpwp-settings", 'none',plugins_url('/assets/icon.png', __FILE__));
	add_submenu_page( "abpwp-settings", __("Danmaku Settings", "abpwp"), __('Settings', "abpwp"), 'administrator',"abpwp-settings", 'abpwp_settings_page');
	add_submenu_page( "abpwp-settings", __("Manage Danmaku", "abpwp"), __('Manage', "abpwp"), 'administrator',"abpwp-manage-comments", 'abpwp_manage_page');
	add_action( 'admin_init', 'register_abpwpsettings' );
}

function register_abpwpsettings() {
	register_setting( 'abpwp-settings-group', 'danmaku_allow_anon' );
	register_setting( 'abpwp-settings-group', 'danmaku_server' );
	register_setting( 'abpwp-settings-group', 'danmaku_id_template' );
	register_setting( 'abpwp-settings-group', 'danmaku_global_maximum' );
}

function abpwp_settings_page(){
	global $wpdb;
	@include_once(dirname(__FILE__) . "/options.php");
}

function abpwp_manage_page(){
	global $wpdb;
	@include_once(dirname(__FILE__) . "/manage.php");
}
?>
