<?php
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo __("Danmaku Settings","abpwp");?></h2>
<form method="post" action="options.php">
    <?php settings_fields( 'abpwp-settings-group' ); ?>
    <?php do_settings_sections( 'abpwp-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php echo __("Danmaku Server","abpwp"); ?></th>
        <td>
			<input type="text" class="regular-text code" name="danmaku_server" value="<?php echo get_option('danmaku_server'); ?>" />
			<p class="description"><?php echo __("Specify the danmaku server's URL. Leave blank to use the current site as the danmaku server.","abpwp"); ?></p>
        </td>
        </tr>
         
        <tr valign="top">
        <th scope="row"><?php echo __("Anonymous Comments","abpwp"); ?></th>
        <td>
			<label for="allow_anon_comment">
				<input type="checkbox" id="allow_anon_comment" name="danmaku_allow_anon" value="true" <?php echo (get_option('danmaku_allow_anon') !== "true" ? "" :' checked="checked"'); ?>/>
				<?php echo __("Allow anonymous users to send danmaku comments", "abpwp"); ?>
			</label>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo __("Max Danmaku","abpwp"); ?></th>
        <td>
			<input type="text" class="regular-text code" name="danmaku_global_maximum" value="<?php echo (int)get_option('danmaku_global_maximum'); ?>" />
			<p class="description"><?php echo __("Set the global maximum danmaku comments per video. A value of 0 means there is no limit.","abpwp"); ?></p>
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo __("Danmaku ID Template","abpwp"); ?></th>
        <td>
			<input type="text" class="regular-text code" name="danmaku_id_template" value="<?php echo get_option('danmaku_id_template', 'abp{pid}'); ?>" />
			<p class="description"><?php echo __("Specify an autogeneration template for danmaku pool IDs. {pid} = Current Post ID","abpwp"); ?></p>
        </td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>
</div>
