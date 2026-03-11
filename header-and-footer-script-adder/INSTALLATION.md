# Header Footer Script Adder - Installation & Usage Guide

## Installation

### Method 1: WordPress Admin (Recommended)
1. Download the `advanced-scripts-manager` folder
2. Create a ZIP file of the entire folder
3. Go to WordPress Admin → Plugins → Add New
4. Click "Upload Plugin" and select your ZIP file
5. Click "Install Now" and then "Activate"

### Method 2: FTP Upload
1. Upload the `advanced-scripts-manager` folder to `/wp-content/plugins/`
2. Go to WordPress Admin → Plugins
3. Find "Header Footer Script Adder" and click "Activate"

## Quick Start Guide

### 1. Access the Plugin
After activation, you'll find "Header Footer Script Adder" in your WordPress admin menu with a custom code icon.

### 2. Add Global Scripts
1. Navigate to **Header Footer Script Adder** in your admin menu
2. Use the three main sections:
   - **Header Scripts**: Code for the `<head>` section
   - **Body Scripts**: Code after the opening `<body>` tag
   - **Footer Scripts**: Code before the closing `</body>` tag

### 3. Configure Conditional Loading
For each script section, choose where it should load:
- **Sitewide**: All pages (default)
- **Homepage Only**: Front page only
- **Posts & Pages**: Individual posts and pages
- **Archive Pages**: Category, tag, and archive pages

### 4. Add Page-Specific Scripts
1. Edit any post or page
2. Look for the "Page-Specific Scripts" meta box
3. Add scripts that will load only on that specific page
4. These work in addition to global scripts

## Common Use Cases

### Google Analytics
```html
<!-- Add to Header Scripts -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Google Tag Manager
```html
<!-- Add to Header Scripts -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>

<!-- Add to Body Scripts -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
```

### Custom CSS
```html
<!-- Add to Header Scripts -->
<style>
.custom-class {
    background-color: #f0f0f0;
    padding: 20px;
    border-radius: 5px;
}
</style>
```

### Facebook Pixel
```html
<!-- Add to Header Scripts -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', 'YOUR_PIXEL_ID');
fbq('track', 'PageView');
</script>
<noscript>
<img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"/>
</noscript>
```

## Features

### ✅ Security Features
- Input sanitization prevents malicious code
- Only administrators can modify scripts
- Nonce verification for all forms
- WordPress coding standards compliant

### ✅ Performance Features
- Minimal database queries
- Conditional loading reduces overhead
- Clean, optimized code
- No impact on page load speed

### ✅ User Experience
- Syntax highlighting with CodeMirror
- Intuitive admin interface
- Comprehensive help documentation
- Block Editor and Classic Editor support

### ✅ Developer Features
- Clean, well-documented code
- Extensible architecture
- WordPress hooks and filters
- Translation ready

## Troubleshooting

### Scripts Not Loading
1. Check if the plugin is activated
2. Verify conditional loading settings
3. Ensure scripts are in the correct format
4. Check for JavaScript errors in browser console

### Syntax Highlighting Not Working
1. Ensure WordPress is version 6.0+
2. Check if CodeMirror is properly loaded
3. Try refreshing the admin page

### Permission Issues
1. Ensure you have administrator privileges
2. Check WordPress user capabilities
3. Verify nonce tokens are working

## File Structure

```
advanced-scripts-manager/
├── advanced-scripts-manager.php    # Main plugin file
├── readme.txt                      # WordPress.org readme
├── uninstall.php                   # Cleanup on uninstall
├── INSTALLATION.md                 # This file
├── admin/                          # Admin functionality
│   ├── class-admin.php
│   ├── partials/
│   │   ├── admin-display.php
│   │   └── meta-box-display.php
│   ├── css/admin-styles.css
│   └── js/admin-scripts.js
├── public/                         # Public functionality
│   └── class-public.php
├── includes/                       # Core classes
│   ├── class-core.php
│   ├── class-loader.php
│   ├── class-activator.php
│   └── class-deactivator.php
└── assets/                         # Plugin assets
    └── icon.svg
```

## Support

For support, feature requests, or bug reports:
- GitHub: https://github.com/advanced-scripts-manager/advanced-scripts-manager
- WordPress.org Support Forums
- Plugin documentation

## Contributing

We welcome contributions! Please see our GitHub repository for contribution guidelines and development setup instructions.

## License

This plugin is licensed under the GPL v2 or later.