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
		<p><?php esc_html_e('Manage custom scripts for your website. Add scripts to the header, body, or footer with conditional loading options.', 'header-and-footer-script-adder'); ?>
		</p>
	</div>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php

                    settings_fields('asm_settings_group');
                    do_settings_sections('asm_settings');
                    submit_button(__('Save Scripts', 'header-and-footer-script-adder'));
                ?>
			</form>
		</div>

		<!-- Right Section (Small) -->
		<div style="flex: 1; padding: 15px; background: #f9f9f9; border: 1px solid #ccc;">
			
			<!-- Affiliate Program Card -->
			<div class="asm-affiliate-section" style="background-color: #f0f7ff; border-left: 4px solid #2271b1; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(34, 113, 177, 0.15);">
				<h3 style="margin-top: 0; font-size: 18px; font-weight: 700; color: #2271b1;"><?php esc_html_e('💰 Earn 20% Commission!', 'header-and-footer-script-adder'); ?></h3>
				<p style="font-size: 13px; line-height: 1.5; color: #3c434a; margin-bottom: 15px;"><?php esc_html_e('Love using Header Footer Script Adder? Join our affiliate program and earn a 20% commission on every Pro sale you refer!', 'header-and-footer-script-adder'); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=asm-affiliate' ) ); ?>" class="button button-primary" style="background-color: #2271b1; color: #ffffff; border: none; font-weight: bold; width: 100%; text-align: center; display: block; padding: 8px 0; height: auto; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-decoration: none;">
					<?php esc_html_e('Apply to Become an Affiliate', 'header-and-footer-script-adder'); ?>
				</a>
			</div>

			<!-- Upgrade to Pro Card -->
			<div class="asm-upgrade-pro-section" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
				<h3 style="color: white; margin-top: 0; font-size: 18px; font-weight: 700;"><?php esc_html_e('🚀 Upgrade to Pro', 'header-and-footer-script-adder'); ?></h3>
				<p style="font-size: 13px; line-height: 1.5; color: #eff6ff; margin-bottom: 15px;">
					<?php esc_html_e('Unlock advanced features to manage and optimize your website scripts like a pro:', 'header-and-footer-script-adder'); ?>
				</p>
				<ul style="font-size: 13px; margin: 0 0 20px 0; padding-left: 20px; color: #eff6ff; list-style-type: disc;">
					<li><strong><?php esc_html_e('Individual Snippets CPT', 'header-and-footer-script-adder'); ?></strong></li>
					<li><strong><?php esc_html_e('Conditional Logic Builder', 'header-and-footer-script-adder'); ?></strong></li>
					<li><strong><?php esc_html_e('One-Click Tracking Pixels', 'header-and-footer-script-adder'); ?></strong></li>
					<li><strong><?php esc_html_e('Script Minification & Defer', 'header-and-footer-script-adder'); ?></strong></li>
				</ul>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=custom-scripts-pricing' ) ); ?>" class="button button-primary" style="background-color: #ffffff; color: #1e3a8a; border: none; font-weight: bold; width: 100%; text-align: center; display: block; padding: 8px 0; height: auto; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-decoration: none;">
					<?php esc_html_e('Get Pro Version Now', 'header-and-footer-script-adder'); ?>
				</a>
			</div>

			<div class="asm-review-section">
				<h3><?php esc_html_e('Enjoying the Plugin?', 'header-and-footer-script-adder'); ?></h3>
				<p><?php esc_html_e('If you love using this plugin, please take a moment to rate it and leave a review. Your feedback helps us improve and motivate us to keep making it better!', 'header-and-footer-script-adder'); ?>
				</p>
				<a href="https://wordpress.org/support/plugin/header-and-footer-script-adder/reviews/#new-post" target="_blank"
					class="button button-primary">
					<?php esc_html_e('Rate & Review', 'header-and-footer-script-adder'); ?>
				</a>
				<p style="background-color: #ffffcc; padding: 10px; border: 1px solid #ccc;">
				<?php esc_html_e('Found a bug? Please report it to us by clicking ', 'header-and-footer-script-adder'); ?>
				<a href="https://onlinetxttools.com/contact/" target="_blank">
					<?php esc_html_e('here', 'header-and-footer-script-adder'); ?>
				</a>
			</p>
			</div>
			<div class="asm-donate-section">
				<div class="asm-donate-box">
					<h3><?php esc_html_e('Support Development', 'header-and-footer-script-adder'); ?></h3>
					<p><?php esc_html_e('If you find this plugin helpful, please consider supporting its development with a small donation.', 'header-and-footer-script-adder'); ?>
					</p>
					<a href="https://www.buymeacoffee.com/mahethekiller" target="_blank" class="button button-secondary" style="font-weight: bold; padding: 5px 15px; height: auto; line-height: 1.5; font-size: 13px; text-decoration: none;">
						<?php esc_html_e('☕ Buy Me A Coffee', 'header-and-footer-script-adder'); ?>
					</a>
					<p class="description">
						<?php esc_html_e('Your support helps maintain and improve this plugin. Thank you!', 'header-and-footer-script-adder'); ?>
					</p>
				</div>
			</div>

			<h3><?php esc_html_e('Usage Instructions', 'header-and-footer-script-adder'); ?></h3>
			<div class="asm-instructions">
				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Global Scripts', 'header-and-footer-script-adder'); ?></h4>
					<p><?php esc_html_e('Scripts added in the sections above will be loaded globally based on your conditional settings:', 'header-and-footer-script-adder'); ?>
					</p>
					<ul>
						<li><strong><?php esc_html_e('Sitewide:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Loads on all pages', 'header-and-footer-script-adder'); ?></li>
						<li><strong><?php esc_html_e('Homepage Only:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Loads only on the front page', 'header-and-footer-script-adder'); ?></li>
						<li><strong><?php esc_html_e('Posts & Pages:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Loads on individual posts and pages', 'header-and-footer-script-adder'); ?></li>
						<li><strong><?php esc_html_e('Archive Pages:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Loads on category, tag, and other archive pages', 'header-and-footer-script-adder'); ?>
						</li>
					</ul>
				</div>

				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Per-Page Scripts', 'header-and-footer-script-adder'); ?></h4>
					<p><?php esc_html_e('You can also add page-specific scripts when editing individual posts or pages. Look for the "Page-Specific Scripts" meta box in the editor.', 'header-and-footer-script-adder'); ?>
					</p>
				</div>

				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Script Locations', 'header-and-footer-script-adder'); ?></h4>
					<ul>
						<li><strong><?php esc_html_e('Header Scripts:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Injected in the &lt;head&gt; section - ideal for CSS, meta tags, and critical JavaScript', 'header-and-footer-script-adder'); ?>
						</li>
						<li><strong><?php esc_html_e('Body Scripts:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Injected after the opening &lt;body&gt; tag - perfect for tracking codes like Google Tag Manager', 'header-and-footer-script-adder'); ?>
						</li>
						<li><strong><?php esc_html_e('Footer Scripts:', 'header-and-footer-script-adder'); ?></strong>
							<?php esc_html_e('Injected before the closing &lt;/body&gt; tag - best for non-critical JavaScript and analytics', 'header-and-footer-script-adder'); ?>
						</li>
					</ul>
				</div>

				<div class="asm-instruction-item">
					<h4><?php esc_html_e('Security Note', 'header-and-footer-script-adder'); ?></h4>
					<p><?php esc_html_e('Only users with administrator privileges can modify scripts. All input is sanitized to prevent malicious code injection while preserving valid HTML, CSS, and JavaScript.', 'header-and-footer-script-adder'); ?>
					</p>
				</div>
			</div>


		</div>
	</div>



	<div class="asm-admin-footer">



		<div class="asm-donate-section">
			<div class="asm-donate-box">
				<h3><?php esc_html_e('Support Development', 'header-and-footer-script-adder'); ?></h3>
				<p><?php esc_html_e('If you find this plugin helpful, please consider supporting its development with a small donation.', 'header-and-footer-script-adder'); ?>
				</p>
				<a href="https://www.buymeacoffee.com/mahethekiller" target="_blank" class="button button-secondary" style="font-weight: bold; padding: 5px 15px; height: auto; line-height: 1.5; font-size: 13px; text-decoration: none;">
					<?php esc_html_e('☕ Buy Me A Coffee', 'header-and-footer-script-adder'); ?>
				</a>
				<p class="description">
					<?php esc_html_e('Your support helps maintain and improve this plugin. Thank you!', 'header-and-footer-script-adder'); ?>
				</p>
			</div>
		</div>

	</div>


</div>