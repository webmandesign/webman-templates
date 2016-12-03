# WebMan Templates

A WordPress plugin that provides a collection of custom [**Beaver Builder** page builder](https://www.wpbeaverbuilder.com/) templates for WordPress themes by [WebMan Design](https://www.webmandesign.eu). Allows convenient and fast way of managing custom template updates without a need to update the compatible themes themselves. The plugin only serves additional custom templates to Beaver Builder page builder interface, thus it is not slowing down your website at all.

## Plugin istallation

Usually this plugin can be automatically installed after you activate a compatible WordPress theme (via [TGM Plugin Activation](http://tgmpluginactivation.com/) script). Alternatively, you can install the plugin manually:

1. Download the latest plugin ZIP file attached to every new [plugin release](/releases).
2. In your WordPress dashboard navigate to the **Plugins &rarr; Add New** screen and click the **Upload** tab.
3. Upload the ZIP file.
4. Navigate to the **Plugins** screen and activate the WebMan Templates plugin.

## Automatic plugin updates

Once you install the plugin, it should receive updates automatically. You just **need to activate the plugin** to enable the automatic updates:

* **Standard WordPress installation (single-site)**:  
  Just install and activate the plugin. You will receive a new plugin updates automatically.
* **Multisite WordPress installation (network)**:  
  In this case you need to activate the plugin for the whole network of your WordPress sites. Otherwise the plugin automatic updates will not work. You will be notified about this if you accidentally activate the plugin for a single site in your network only. The plugin only works on sites that use a compatible themes.

This plugin is also compatible with [**Github Updater** plugin](https://github.com/afragen/github-updater/wiki/Installation), so if you have Github Updater installed and active, the WebMan Templates plugin will be updated automatically as well. (This may be even better solution for WordPress multisite installations.)

## WordPress theme integration

This is information intended for a theme developers (currently for [WebMan Design themes](https://www.webmandesign.eu) only):

* The theme **must** declare support for the plugin with a `add_theme_support( 'webman-templates' );` code.
* The theme **can** declare support for global templates provided with the plugin (with a `add_theme_support( 'webman-templates-global' );` code). Otherwise only the theme specific templates are served.
* Template thumbnails (featured images) can be served locally from the plugin once you upload them to the `templates/THEME_SLUG/thumbs` directory. In that case the exported template files (the Beaver Builder `.dat` files) must not contain the full URL to the image, only the image file name (with possible relative folder path to the image). The best size for template thumbnails is **256px wide** (the height of the image is up to you).

## Versioning info

Plugin version numbers consist of `MAJOR.MINOR.PATCH` numbers:

* **MAJOR** - Created when a new theme is added (and thus a new theme templates files are added).
* **MINOR** - When a plugin functionality is added, when a theme templates files require an update.
* **PATCH** - When a plugin main functionality is patched or a minor theme templates file issue is fixed.

---

&copy; **WebMan Templates** by [WebMan Design](https://www.webmandesign.eu) | Distributed under terms of [GPL-3.0 license](https://www.gnu.org/licenses/gpl-3.0.html)
