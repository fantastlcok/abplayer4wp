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
add_action('wp_enqueue_scripts', 'abpwp_add_styles');

function abpwp_add_styles(){
	wp_enqueue_style('abpwp-base-css', plugins_url('/assets/base.css', __FILE__));
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
	
	$scripts = '<script src="' . plugins_url('/assets/CommentCore.js', __FILE__) . '" type="text/javascript"></script>';
	$scripts.= '<script src="' . plugins_url('/assets/Parsers.js', __FILE__) . '" type="text/javascript"></script>';
	$scripts.= '<script src="' . plugins_url('/assets/libxml.js', __FILE__) . '" type="text/javascript"></script>';
	$scripts.= '<script src="' . plugins_url('/assets/player.js', __FILE__) . '" type="text/javascript"></script>';
	$body = '<div id="player-' . $dmid . '" tabindex="1"></div>
			<video id="abp-video-' . $dmid . '" autobuffer="true" data-setup="{}">'. abpwp_video_to_source($formats) .'</video>';
	$init = '<script type="text/javascript">
			window.addEventListener("load",function(){
				var inst = ABP.create(document.getElementById("player-' . $dmid . '"),{
					"src":document.getElementById("abp-video-' . $dmid .'"),
					"width":640,
					"height":480
				});
				CommentLoader("//parsee/~jim/devel/ABPlayerHTML5/build/comment.xml", inst.cmManager);
				inst.txtText.focus();
				inst.txtText.addEventListener("keydown", function(e){
					if(e && e.keyCode === 13){
						if(/^!/.test(this.value)) return; //Leave the internal commands
						inst.txtText.value = "";
					}
				});
				window.abpinst = inst;
			});
		</script>';
	return $scripts . $body . $init;
}

add_action('wp_ajax_danmaku', 'abpwp_ajax_danmaku' );
add_action('wp_ajax_nopriv_danmaku', 'abpwp_ajax_danmaku' );

function abpwp_ajax_danmaku(){
	
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
	@include_once(dirname(__FILE__) . "/options.php");
}

function abpwp_manage_page(){
	@include_once(dirname(__FILE__) . "/manage.php");
}
?>
