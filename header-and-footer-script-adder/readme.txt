=== Header Footer Script Adder – Insert Code in Header, Body & Footer ===
Contributors: mahethekiller
Tags: header, footer, body, insert code, add scripts, google analytics, facebook pixel, tracking, custom css, javascript, meta tags, seo, tag manager, chat widgets
Requires at least: 6.0
Tested up to: 6.8.1
Donate link: https://www.buymeacoffee.com/mahethekiller
Requires PHP: 7.4
Stable tag: 2.0.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily add custom scripts and code to your WordPress site’s header, body, or footer. Perfect for Google Analytics, Tag Manager, pixels, meta tags, custom CSS, and JavaScript.

== Description ==

**Header Footer Script Adder** is a powerful and user-friendly WordPress plugin that lets you easily insert **custom HTML, CSS, and JavaScript** into your site’s header, body, or footer.  

You don’t need to edit theme files or worry about losing your code after updates. This plugin keeps your scripts safe, reusable, and flexible with **conditional loading and per-page overrides**.

### ✨ Features
* Add scripts to **Header (`<head>`), Body (after `<body>`), or Footer (before `</body>`)**
* **Conditional Loading** – load scripts sitewide, homepage only, posts/pages, or archives
* **Per-Page Overrides** – add unique code for specific posts and pages
* **Code Editor with Syntax Highlighting** (CodeMirror)
* **Safe Input Handling** – sanitization without breaking valid HTML, CSS, or JS
* **Lightweight and Fast** – minimal database queries
* **Block Editor & Classic Editor Compatible**
* **Multisite Ready & Translation Ready**

### ✅ Perfect For
* Google Analytics / GA4 tracking code
* Google Tag Manager
* Facebook Pixel and ad tracking
* Chat widgets (WhatsApp, Crisp, Tawk.to, etc.)
* Custom CSS and JavaScript
* SEO and verification meta tags (Google, Bing, Pinterest)
* A/B testing scripts
* External fonts and resources

### ⚙️ Conditional Loading Options
* **Sitewide** (all pages)
* **Homepage only**
* **Posts & Pages only**
* **Archive pages**

### 🔒 Security & Performance
* Sanitized input to prevent malicious injections
* Nonce verification for safe form submissions
* Admin-only access for configuration
* Clean uninstall process (no leftover data)
* Minimal performance impact

### 👨‍💻 Developer Friendly
* Extensible, documented code
* WordPress coding standards compliant
* Translation-ready `.pot` file included
* Works with any theme

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install via the WordPress dashboard.
2. Activate the plugin from **Plugins → Installed Plugins**.
3. Go to **Header Footer Script Adder** in your admin menu.
4. Paste your scripts and configure loading conditions.
5. Save settings and done!

= Manual Installation =

1. Download the plugin `.zip`.
2. In **WordPress Admin → Plugins → Add New → Upload Plugin**, select the zip file.
3. Install and activate.
4. Configure from **Settings → Header Footer Script Adder**.

== Frequently Asked Questions ==

= Is this plugin safe to use? =  
Yes. Input is sanitized, and only admins can add or edit scripts. It follows WordPress security best practices.

= Will it slow down my website? =  
No. It’s lightweight and optimized. Scripts only load where needed.

= Can I add scripts to specific pages only? =  
Yes. Use the per-page overrides in the post/page editor.

= Does it support Gutenberg (Block Editor)? =  
Yes. Works with both Gutenberg and Classic Editor.

= Can I use it on multisite? =  
Yes. Each site can have its own configuration.

= Can I add both Google Analytics and Facebook Pixel together? =  
Yes. You can add multiple scripts in header, body, and footer.

= Will my scripts stay if I switch themes? =  
Yes. Code is stored in the database, not in theme files.

= How do I remove plugin data completely? =  
When uninstalling, you’ll have the option to remove all saved scripts from your database.

= Can I insert chat widgets or meta tags? =  
Yes. The plugin supports any valid HTML, CSS, or JavaScript code.

== Screenshots ==

1. **Main Settings Page** – Add scripts globally.  
2. **Code Editor** – Syntax highlighting for easy editing.  
3. **Conditional Options** – Control script placement (sitewide, homepage, archives, etc.).  
4. **Per-Page Overrides** – Unique scripts per post or page.  
5. **Help Section** – Usage instructions and FAQs.  

== Changelog ==

= 1.0.0 =
* Initial release.

= 1.2.0 =
* Added new features and bug fixes.
* Improved UI for easier code management.

= 2.0.3 =
* Major release.
* Added global header, body, and footer script support.
* Conditional loading (sitewide, homepage, posts, archives).
* Per-page script overrides.
* Integrated CodeMirror editor.
* Improved sanitization and security.
* Block Editor and Classic Editor compatibility.
* Performance optimizations.
* Updated documentation.

== Upgrade Notice ==

= 2.0.6 =
Fixed few security issues.

= 2.0.5 =
Fixed few issues.

= 2.0.3 =
This is a major release. Backup your scripts before updating as older settings may be replaced.

== Donations ==

If you enjoy this plugin and want to support development, you can buy me a coffee:  
👉 [https://www.buymeacoffee.com/mahethekiller](https://www.buymeacoffee.com/mahethekiller)

== Support & Documentation ==

For support, feature requests, or documentation, please visit the **support forum** on WordPress.org.

== Privacy Policy ==

This plugin does not collect or share personal data. It only loads the scripts you provide.  
Any data collection depends on the external scripts you choose to add (e.g., Google Analytics, Facebook Pixel, chat widgets).
