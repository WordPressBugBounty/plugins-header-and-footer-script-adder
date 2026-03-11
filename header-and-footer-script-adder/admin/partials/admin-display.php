<?php
    /**
     * Provide a admin area view for the plugin
     *
     * This file is used to markup the admin-facing aspects of the plugin.
     *
     * @link       https://github.com/advanced-scripts-manager
     * @since      2.0.3
     *
     * @package    AdvancedScriptsManager
     * @subpackage AdvancedScriptsManager/admin/partials
     */

    // If this file is called directly, abort.
    if (! defined('WPINC')) {
        die;
    }
?>

<div class="wrap">




	<div style="display: flex; gap: 20px;">
		<!-- Left Section (Large) -->
		<div style="flex: 3; padding: 15px; background: #fff; border: 1px solid #ccc;">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			
			
			

	<div class="asm-admin-header">
		<p><?php esc_html_e('Manage custom scripts for your website. Add scripts to the header, body, or footer with conditional loading options.', 'advanced-scripts-manager'); ?>
		</p>
	</div>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php

                    settings_fields('asm_settings_group');
                    do_settings_sections('asm_settings');
                    submit_button(__('Save Scripts', 'advanced-scripts-manager'));
                ?>
			</form>
		</div>

		<!-- Right Section (Small) -->
		<div style="flex: 1; padding: 15px; background: #f9f9f9; border: 1px solid #ccc;">
			<div class="asm-review-section">
				<h3><?php esc_html_e('Enjoying the Plugin?', 'header-footer-script-adder'); ?></h3>
				<p><?php esc_html_e('If you love using this plugin, please take a moment to rate it and leave a review. Your feedback helps us improve and motivate us to keep making it better!', 'header-footer-script-adder'); ?>
				</p>
				<a href="https://wordpress.org/support/plugin/header-and-footer-script-adder/reviews/#new-post" target="_blank"
					class="button button-primary">
					<?php esc_html_e('Rate & Review', 'header-footer-script-adder'); ?>
				</a>
				<p style="background-color: #ffffcc; padding: 10px; border: 1px solid #ccc;">
				<?php esc_html_e('Found a bug? Please report it to us by clicking ', 'advanced-scripts-manager'); ?>
				<a href="https://onlinetxttools.com/contact/" target="_blank">
					<?php esc_html_e('here', 'advanced-scripts-manager'); ?>
				</a>
			</p>
			</div>
			<div class="asm-donate-section">
				<div class="asm-donate-box">
					<h3><?php esc_html_e('Support Development', 'header-footer-script-adder'); ?></h3>
					<p><?php esc_html_e('If you find this plugin helpful, please consider supporting its development with a small donation.', 'header-footer-script-adder'); ?>
					</p>
					<!-- <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="ESSBBUCDBPAG2">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"
							border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"
							style="border: none;">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1"
							height="1">
					</form> -->
					<a href="https://www.buymeacoffee.com/mahethekiller" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>
					<p class="description">
						<?php esc_html_e('Your support helps maintain and improve this plugin. Thank you!', 'header-footer-script-adder'); ?>
					</p>
				</div>
			</div>

			<h3><?php esc_html_e('Usage Instructions', 'advanced-scripts-manager'); ?></h3>
			<div class="asm-instructions">
				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Global Scripts', 'advanced-scripts-manager'); ?></h4>
					<p><?php esc_html_e('Scripts added in the sections above will be loaded globally based on your conditional settings:', 'advanced-scripts-manager'); ?>
					</p>
					<ul>
						<li><strong><?php esc_html_e('Sitewide:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Loads on all pages', 'advanced-scripts-manager'); ?></li>
						<li><strong><?php esc_html_e('Homepage Only:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Loads only on the front page', 'advanced-scripts-manager'); ?></li>
						<li><strong><?php esc_html_e('Posts & Pages:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Loads on individual posts and pages', 'advanced-scripts-manager'); ?></li>
						<li><strong><?php esc_html_e('Archive Pages:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Loads on category, tag, and other archive pages', 'advanced-scripts-manager'); ?>
						</li>
					</ul>
				</div>

				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Per-Page Scripts', 'advanced-scripts-manager'); ?></h4>
					<p><?php esc_html_e('You can also add page-specific scripts when editing individual posts or pages. Look for the "Page-Specific Scripts" meta box in the editor.', 'advanced-scripts-manager'); ?>
					</p>
				</div>

				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Script Locations', 'advanced-scripts-manager'); ?></h4>
					<ul>
						<li><strong><?php esc_html_e('Header Scripts:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Injected in the &lt;head&gt; section - ideal for CSS, meta tags, and critical JavaScript', 'advanced-scripts-manager'); ?>
						</li>
						<li><strong><?php esc_html_e('Body Scripts:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Injected after the opening &lt;body&gt; tag - perfect for tracking codes like Google Tag Manager', 'advanced-scripts-manager'); ?>
						</li>
						<li><strong><?php esc_html_e('Footer Scripts:', 'advanced-scripts-manager'); ?></strong>
							<?php esc_html_e('Injected before the closing &lt;/body&gt; tag - best for non-critical JavaScript and analytics', 'advanced-scripts-manager'); ?>
						</li>
					</ul>
				</div>

				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Security Note', 'advanced-scripts-manager'); ?></h4>
					<p><?php esc_html_e('Only users with administrator privileges can modify scripts. All input is sanitized to prevent malicious code injection while preserving valid HTML, CSS, and JavaScript.', 'advanced-scripts-manager'); ?>
					</p>
				</div>
			</div>


			<div class="asm-promote-plugin">
				<h3><?php esc_html_e('Related Plugin:', 'advanced-scripts-manager'); ?></h3>
				<p><?php esc_html_e('Are you tired of manually translating your WordPress posts and pages? Introducing the ultimate solution:', 'advanced-scripts-manager'); ?></p>
				<a href="https://wordpress.org/plugins/translate-post-to-language/" target="_blank">
					<img src="<?php echo esc_url(plugins_url() . '/header-and-footer-script-adder/assets/tptl.png'); ?>" alt="Translate Post to Language" width="128" height="128" />
					<h4>Translate Post to Language</h4>
					<p><?php esc_html_e('Automatically translate your WordPress posts and pages to the languages of your choice, with just a few clicks. Supports multiple languages and translation services.', 'advanced-scripts-manager'); ?></p>
				</a>
			</div>

		</div>
	</div>




	<div class="asm-admin-footer">



		<div class="asm-donate-section">
			<div class="asm-donate-box">
				<h3><?php esc_html_e('Support Development', 'header-footer-script-adder'); ?></h3>
				<p><?php esc_html_e('If you find this plugin helpful, please consider supporting its development with a small donation.', 'header-footer-script-adder'); ?>
				</p>
				<!-- <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="ESSBBUCDBPAG2">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0"
						name="submit" alt="PayPal - The safer, easier way to pay online!" style="border: none;">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1"
						height="1">
				</form> -->
				<a href="https://www.buymeacoffee.com/mahethekiller" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>
				<p class="description">
					<?php esc_html_e('Your support helps maintain and improve this plugin. Thank you!', 'header-footer-script-adder'); ?>
				</p>
			</div>
		</div>
<script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="mahethekiller" data-description="Support me on Buy me a coffee!" data-message="Thank You for using this plugin." data-color="#FF813F" data-position="Right" data-x_margin="18" data-y_margin="18"></script>

	</div>


</div>