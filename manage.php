<?php

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo __("Manage Danmaku","abpwp");?></h2>
	<?php
		if($_POST["mode"] === "importXmlfile" || $_POST["mode"] === "importMukiodb"):
			@require_once(dirname(__FILE__) . "/import.php");
			$error = false;
			$file = $_FILES[$_POST["mode"] === "importXmlfile" ? "xmldanmaku" : "mukiodb"];
			if($file !== null && !empty($file) && $file["error"] === 0){
				$count = abpwp_import_danmaku($_POST["mode"],$file, $_POST["videoid"]);
			}else{
				$error = true;
			}
			if(!$error):
	?>
		<div class="updated settings-updated"><p>
			<strong><?php echo __("Danmaku Import: ", "abpwp");?></strong>
			<?php printf(__("You've successfully imported %s comments!","abpwp"), $count);?>
		</p></div>
	<?php
		else:
	?>
		<div class="error settings-error"><p>
			<strong><?php echo __("Danmaku Import: ", "abpwp");?></strong>
			<?php printf(__("Something went wrong. No comments imported! Are you sure you provided a file?","abpwp"));?>
		</p></div>
	<?php
		endif;
		endif;
	?>
	<?php if(get_option('danmaku_server') != ""): ?>
		<div class="error settings-error"><p>
			<strong><?php echo __("Danmaku Server Error: ", "abpwp");?></strong>
			<?php printf(__("You have set up an alternate danmaku server at %s. You will not be able to manage danmaku comments using this interface.","abpwp"), preg_replace("~<~iUs","&lt;",get_option('danmaku_server')));?>
		</p></div>
		<a href="javascript:history.back();" class="button button-primary">Back</a>
	<?php else: 
		$danmaku = $wpdb->get_results("SELECT COUNT(*) as `count` FROM `" . $wpdb->prefix . "danmaku`");
		if(isset($danmaku[0])){
			$danmaku_count = $danmaku[0]->count;
		}else{
			$danmaku_count = 0;
		}
		$pagenum = (int)$_GET["pn"];
	?> 
	<p id="abpwp-search" class="search-box" style="margin:10px 0 10px 0;"><?php printf(__("Total: %s danmaku ", "abpwp"), $danmaku_count); ?>
		<input type="text" class="regular-text code"> <input type="submit" class="button" value="<?php echo __("Search Danmaku", "abpwp");?>"></p>
	<table class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" width="80">Video ID</th>
				<th scope="col">Content</th>
				<th scope="col" width="100">Author</th>
				<th scope="col" width="150">Time</th>
				<th scope="col" width="50">Type</th>
				<th scope="col" width="160">Action</th>
			</tr>
		</thead>
		<tr>
			<?php
				$results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "danmaku` LIMIT " . $pagenum*20 . "," . ($pagenum*20 + 20));
				foreach($results as $result){
					echo "<tr>";
					echo "<td>" . $result->pool . "</td>";
					echo "<td>" . htmlspecialchars($result->text) . "</td>";
					echo "<td>" . $result->author . "</td>";
					echo "<td>" . $result->date . "</td>";
					echo "<td>" . $result->type . "</td>";
					echo "<td>
						<form method=\"post\" action=\"\">
							<input type=\"hidden\" name=\"id\" value=\"" . $result->id . "\">
							<input type=\"submit\" name=\"delete\" value=\"Delete\" class=\"button\">
							<input type=\"submit\" name=\"ban\" value=\"Ban User\" class=\"button\">
						</form>
					</td>";
					echo "</tr>";
				}
			?>
		</tr>
	</table>
	<div class="tablenav">
		<div class="tablenav-pages">
			<a href="<?php 
				echo admin_url("admin.php?page=abpwp-manage-comments&pn=" . ($pagenum - 1 >= 0 ? $pagenum - 1 : 0));
			?>" class="last-page">&laquo;</a><a href="<?php 
				echo admin_url("admin.php?page=abpwp-manage-comments&pn=" . ($pagenum + 1));
			?>" class="next-page">&raquo;</a>
		</div>
	</div>
	
	<div style="border:4px dashed #aeaeae;padding:20px;margin-top:10px;">
		<h3 style="margin:0;"><?php echo __("Import","abpwp"); ?></h3>
		<div style="max-width:600px;">
			<form action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="mode" value="importMukiodb">
				<h4><?php echo __("Import Danmaku database from MukioPlayer4WP:", "abpwp")?></h4>
				<input type="submit" class="button button-primary" style="float:right;" value="<?php echo __("Import From MukioWP", "abpwp");?>">
				<?php echo __("File");?>: <input type="file" name="mukiodb"><br>
				
			</form>
		</div>
		<div style="max-width:600px;">
			<form action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="mode" value="importXmlfile">
				<h4><?php echo __("Import Danmaku file (xml)", "abpwp")?></h4>
				<input type="submit" class="button button-primary" style="float:right;" value="<?php echo __("Import From xml", "abpwp");?>">
				VID: <input type="text" name="videoid"><br>
				<?php echo __("File");?>: <input type="file" name="xmldanmaku">
				<br>
			</form>
		</div>
	</div>
	<?php endif;?>
</div>
