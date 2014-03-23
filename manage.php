<?php
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo __("Manage Danmaku","abpwp");?></h2>
	<?php if(get_option('danmaku_server') != ""): ?>
		<div class="error settings-error"><p>
			<strong><?php echo __("Danmaku Server Error: ", "abpwp");?></strong>
			<?php printf(__("You have set up an alternate danmaku server at %s. You will not be able to manage danmaku comments using this interface.","abpwp"), preg_replace("~<~iUs","&lt;",get_option('danmaku_server')));?>
		</p></div>
		<a href="javascript:history.back();" class="button button-primary">Back</a>
	<?php else: ?> 
	<p id="abpwp-search" class="search-box" style="margin:10px 0 10px 0;"><input type="text" class="regular-text code"> <input type="submit" class="button" value="<?php echo __("Search Danmaku", "abpwp");?>"></p>
	<table class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" width="80">Video ID</th>
				<th scope="col">Content</th>
				<th scope="col" width="100">Author</th>
				<th scope="col" width="100">Time</th>
				<th scope="col" width="50">Type</th>
				<th scope="col" width="180">Action</th>
			</tr>
		</thead>
		<tr>
			<td>av0001</td>
			<td>Yoooooooooo</td>
			<td>192.168.131.10</td>
			<td>2012-12-31 22:22:00</td>
			<td>7</td>
			<td>
				<form method="post" action="">
					<input type="submit" name="delete" value="Delete" class="button">
					<input type="submit" name="ban" value="Ban User" class="button">
				</form>
			</td>
		</tr>
	</table>
	<h3><?php echo __("Import","abpwp"); ?></h3>
	<p>
		<form action="" method="POST">
			<h4><?php echo __("Import Danmaku database from MukioPlayer4WP:", "abpwp")?></h4>
			<?php echo __("File");?>: <input type="file" name="mukiodb"><br>
			<input type="submit" class="button button-primary" value="<?php echo __("Import From MukioWP", "abpwp");?>">
		</form>
	</p>
	<p>
		<form action="" method="POST">
			<h4><?php echo __("Import Danmaku file (xml)", "abpwp")?></h4>
			VID: <input type="text" name="videoid"><br>
			<?php echo __("File");?>: <input type="file" name="xmldanmaku">
			<br>
			<input type="submit" class="button button-primary" value="<?php echo __("Import From MukioWP", "abpwp");?>">
		</form>
	</p>
	<?php endif;?>
</div>
