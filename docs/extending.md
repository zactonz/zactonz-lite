# Extension points

## Constants

`functions.php` defines three constants before anything else loads. They are guarded with
`defined()`, so a mu-plugin loaded earlier can set its own value and the theme will use it.

| Constant | Value |
|---|---|
| `ZACTONZ_LITE_VERSION` | `1.0.0` — used as the `$ver` argument on every enqueue |
| `ZACTONZ_LITE_DIR` | `get_template_directory()` |
| `ZACTONZ_LITE_URI` | `get_template_directory_uri()` |

## The theme defines filters, not actions

There are no custom actions. Everything the theme exposes is a filter, and there are seven of them.
If you need to run code at a particular moment, hook the core action the theme itself hooks — they
are listed at the bottom of this page.

## Turning a module off

`functions.php` loads three files, each behind its own filter:

```php
foreach ( array( 'setup', 'assets', 'performance' ) as $module ) {
	if ( apply_filters( "zactonz_lite_load_{$module}", true ) ) {
		require_once ZACTONZ_LITE_DIR . '/inc/' . $module . '.php';
	}
}
```

| Filter | Default | Skipping it means |
|---|---|---|
| `zactonz_lite_load_setup` | `true` | No text domain, no editor style, no pattern category, no block styles, no comment-heading fix |
| `zactonz_lite_load_assets` | `true` | No stylesheet and no font preloading |

These run at theme load, so they must be set from a must-use plugin:

```php
// wp-content/mu-plugins/zactonz-lite-modules.php
add_filter( 'zactonz_lite_load_assets', '__return_false' );
```

A plugin loaded normally is too late — `functions.php` has already run.

## Assets

### `zactonz_lite_enable_font_preload`

```php
apply_filters( 'zactonz_lite_enable_font_preload', true );
```

The theme preloads a bundled font **only when the active global styles actually reference its
slug** — it reads `wp_get_global_styles()` and matches `font-family|<slug>` and
`--wp--preset--font-family--<slug>`. With the default variation no font is referenced, so nothing is
preloaded and nothing is downloaded. Select Editorial and exactly one preload appears for the
bundled Instrument Serif woff2.

Return `false` to emit no preloads at all. The fonts still load when text uses them; you lose the
head start, not the font.

## Performance


