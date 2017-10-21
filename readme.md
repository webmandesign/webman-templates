# WebMan Templates

See `readme.txt` plugin for description.
Information below is for developers only/mainly.


## WordPress theme integration

* If the theme is not supported in the plugin, it provides global, theme-independent templates only.
* If a theme adds a supports for the plugin, it must contain the theme's template files. In this case the plugin won't load the global, theme-independent templates, unless the theme claims support for them with `add_theme_support( 'webman-templates-global' );` in theme setup.
* Template thumbnails (featured images) can be served locally from the plugin folder once they are uploaded to `templates/THEME_SLUG/thumbs` folder. In that case the exported template files (the Beaver Builder `.dat` files) must not contain the full URL to the image, only the image file name (with possible relative folder path to the image), or, if the template slug is provided, it will also be used for thumbnail file name. The best size for template thumbnails is **256px wide** (the height of the image is up to you).


## Versioning info

Plugin version numbers consist of `MAJOR.MINOR.PATCH` numbers:

* **MAJOR** - Created when a new theme is added (and thus a new theme templates files are added).
* **MINOR** - When a plugin functionality is added, when a theme templates files require an update.
* **PATCH** - When a plugin main functionality is patched or a minor theme templates file issue is fixed.

---

&copy; **WebMan Templates** by [WebMan Design](https://www.webmandesign.eu) | Distributed under terms of [GPL-3.0 license](https://www.gnu.org/licenses/gpl-3.0.html)
