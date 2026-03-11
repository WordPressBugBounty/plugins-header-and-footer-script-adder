<?php
    /**
     * Provide a meta box view for the plugin
     *
     * This file is used to markup the meta box for post/page specific scripts.
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

<div class="asm-meta-box">
	<p class="description">
		<?php esc_html_e('Add custom scripts that will load only on this specific post or page. These scripts will be loaded in addition to any global scripts.', 'advanced-scripts-manager'); ?>

	</p>
	<p class="description" style="color: #b32d2e;">
	<?php esc_html_e('Found a bug? Please report it to us by clicking ', 'advanced-scripts-manager'); ?>
				<a href="https://onlinetxttools.com/contact/" target="_blank">
					<?php esc_html_e('here', 'advanced-scripts-manager'); ?>
				</a>
				</p>
				<p></p>

	<div class="asm-meta-field">
		<label for="asm_header_scripts">
			<strong><?php esc_html_e('Header Scripts', 'advanced-scripts-manager'); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e('Scripts added here will be injected into the <head> section for this page only.', 'advanced-scripts-manager'); ?>
		</p>
		<textarea
			id="asm_header_scripts"
			name="asm_header_scripts"
			rows="8"
			cols="80"
			class="asm-code-editor large-text"
			placeholder="<?php esc_attr_e('Enter your header scripts here...', 'advanced-scripts-manager'); ?>"
		><?php echo esc_textarea($header_scripts); ?></textarea>
	</div>

	<div class="asm-meta-field">
		<label for="asm_body_scripts">
			<strong><?php esc_html_e('Body Scripts', 'advanced-scripts-manager'); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e('Scripts added here will be injected immediately after the opening <body> tag for this page only.', 'advanced-scripts-manager'); ?>
		</p>
		<textarea
			id="asm_body_scripts"
			name="asm_body_scripts"
			rows="8"
			cols="80"
			class="asm-code-editor large-text"
			placeholder="<?php esc_attr_e('Enter your body scripts here...', 'advanced-scripts-manager'); ?>"
		><?php echo esc_textarea($body_scripts); ?></textarea>
	</div>

	<div class="asm-meta-field">
		<label for="asm_footer_scripts">
			<strong><?php esc_html_e('Footer Scripts', 'advanced-scripts-manager'); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e('Scripts added here will be injected before the closing </body> tag for this page only.', 'advanced-scripts-manager'); ?>
		</p>
		<textarea
			id="asm_footer_scripts"
			name="asm_footer_scripts"
			rows="8"
			cols="80"
			class="asm-code-editor large-text"
			placeholder="<?php esc_attr_e('Enter your footer scripts here...', 'advanced-scripts-manager'); ?>"
		><?php echo esc_textarea($footer_scripts); ?></textarea>
	</div>

	<div class="asm-meta-info">
		<h4><?php esc_html_e('Usage Tips:', 'advanced-scripts-manager'); ?></h4>
		<ul>
			<li><?php esc_html_e('Use header scripts for CSS styles, meta tags, and critical JavaScript that needs to load early.', 'advanced-scripts-manager'); ?></li>
			<li><?php esc_html_e('Use body scripts for tracking codes like Google Tag Manager that need to fire immediately.', 'advanced-scripts-manager'); ?></li>
			<li><?php esc_html_e('Use footer scripts for non-critical JavaScript and analytics that can load after the page content.', 'advanced-scripts-manager'); ?></li>
			<li><?php esc_html_e('All scripts are automatically sanitized for security while preserving valid HTML, CSS, and JavaScript.', 'advanced-scripts-manager'); ?></li>
		</ul>
	</div>
	
</div>

<style>
.asm-meta-box .asm-meta-field {
	margin-bottom: 20px;
}

.asm-meta-box .asm-meta-field label {
	display: block;
	margin-bottom: 5px;
}

.asm-meta-box .description {
	margin-bottom: 8px;
	font-style: italic;
	color: #666;
}

.asm-meta-box .asm-code-editor {
	font-family: Consolas, Monaco, monospace;
	font-size: 13px;
	line-height: 1.4;
}

.asm-meta-info {
	background: #f9f9f9;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 15px;
	margin-top: 20px;
}

.asm-meta-info h4 {
	margin-top: 0;
	margin-bottom: 10px;
}

.asm-meta-info ul {
	margin: 0;
	padding-left: 20px;
}

.asm-meta-info li {
	margin-bottom: 5px;
}



        /* Main meta box container */
        .asm-meta-box {
            background: #f9fbff;
            border: 2px solid #d0e3ff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
        }

        /* Section fields */
        .asm-meta-field {
            margin-bottom: 25px;
            padding: 15px;
            background: #ffffff;
            border-left: 5px solid #4CAFEF;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        /* Labels */
        .asm-meta-field label {
            display: block;
            font-size: 15px;
            color: #333;
            margin-bottom: 5px;
        }

        /* Description text */
        .asm-meta-field .description {
            font-size: 13px;
            color: #555;
            margin-bottom: 8px;
        }

        /* Code editor look for textareas */
        .asm-code-editor {
            font-family: "Fira Code", "Courier New", monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            border: 1px solid #3c3c3c;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            resize: vertical;
            min-height: 150px;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.5);
            line-height: 1.4;
            font-size: 14px;
        }
        .asm-code-editor:focus {
            outline: none;
            border-color: #4CAFEF;
            box-shadow: 0 0 6px #4CAFEF;
        }

        /* Info section at bottom */
        .asm-meta-info {
            margin-top: 20px;
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 6px;
        }
        .asm-meta-info h4 {
            margin-top: 0;
            color: #0d47a1;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .asm-meta-info ul {
            padding-left: 20px;
            margin: 0;
        }
        .asm-meta-info li {
            font-size: 13px;
            margin-bottom: 6px;
        }

</style>