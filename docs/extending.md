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
| `zactonz_lite_load_assets` | `true` | No `main.css`, no font preloading, no navigation stylesheet substitution |
| `zactonz_lite_load_performance` | `true` | Speculation rules fall back to core's defaults |

These run at theme load, so they must be set from a must-use plugin:

```php
// wp-content/mu-plugins/no-speculation.php
add_filter( 'zactonz_lite_load_performance', '__return_false' );
```

A plugin loaded normally is too late — `functions.php` has already run.

## Assets

### `zactonz_lite_slim_navigation_style`

```php
apply_filters( 'zactonz_lite_slim_navigation_style', true );
```

Core's `wp-block-navigation` stylesheet is 20,776 bytes, most of it responsive-overlay and submenu
variants this theme's header never renders. When this returns `true`, the theme deregisters that
handle and re-registers it against its own 1,628-byte file, saving 19,148 bytes on every page.

Return `false` to keep core's stylesheet — do that if you replace the header with one that uses an
overlay menu or submenus. See [Navigation and menus](navigation.md).

```php
add_filter( 'zactonz_lite_slim_navigation_style', '__return_false' );
```

The substitution runs on `wp_enqueue_scripts` at priority 20 and bails if the handle is not
registered, so the order relative to other plugins is safe.

### `zactonz_lite_preload_fonts`

```php
apply_filters( 'zactonz_lite_preload_fonts', true );
```

The theme preloads a bundled font **only when the active global styles actually reference its
slug** — it reads `wp_get_global_styles()` and matches `font-family|<slug>` and
`--wp--preset--font-family--<slug>`. With the default variation no font is referenced, so nothing is
preloaded and nothing is downloaded. Select Editorial and exactly one preload appears for the
bundled Instrument Serif woff2.

Return `false` to emit no preloads at all. The fonts still load when text uses them; you lose the
head start, not the font.

## Performance

### `zactonz_lite_speculation`

```php
apply_filters( 'zactonz_lite_speculation', $config );
```

Receives the configuration array the theme is about to hand core's Speculation Rules API, after the
theme has set `mode` to `prerender` and `eagerness` to `moderate`. Return it modified.

```php
add_filter( 'zactonz_lite_speculation', function ( $config ) {
	$config['eagerness'] = 'conservative';
	return $config;
} );
```

Two things happen before your filter runs. If core passes `null` or a non-array the theme returns it
untouched, so your filter is never called. And if `WooCommerce` or `Easy_Digital_Downloads` is
loaded the theme returns core's configuration unchanged and **your filter is not called either** —
prerendering issues real requests, which is unsafe for carts. To raise it on a commerce site, hook
`wp_speculation_rules_configuration` directly at a later priority.

### `zactonz_lite_speculation_exclude_paths`

```php
apply_filters( 'zactonz_lite_speculation_exclude_paths', array(
	'/cart/*', '/checkout/*', '/my-account/*', '/*add-to-cart*',
) );
```

Paths never speculatively loaded. The return value is merged with whatever core already excludes, so
returning your own array adds to the list rather than replacing core's. Unlike the configuration
filter, this one runs whatever plugins are active.

```php
add_filter( 'zactonz_lite_speculation_exclude_paths', function ( $paths ) {
	$paths[] = '/booking/*';
	return $paths;
} );
```

## Core hooks the theme attaches to

Useful when you need to run before or after the theme, or to unhook it.

| Hook | Callback | Priority |
|---|---|---|
| `after_setup_theme` | `zactonz_lite_setup` | 10 |
| `init` | `zactonz_lite_register_pattern_category` | 10 |
| `init` | `zactonz_lite_register_block_styles` | 10 |
| `comment_form_defaults` | `zactonz_lite_comment_form_heading` | 10 |
| `wp_enqueue_scripts` | `zactonz_lite_enqueue_styles` | 10 |
| `wp_enqueue_scripts` | `zactonz_lite_slim_navigation_style` | 20 |
| `wp_preload_resources` | `zactonz_lite_preload_fonts` | 10 |
| `wp_speculation_rules_configuration` | `zactonz_lite_speculation_configuration` | 10 |
| `wp_speculation_rules_href_exclude_paths` | `zactonz_lite_speculation_exclusions` | 10 |

To remove one, use `remove_action` / `remove_filter` with the same name and priority from a
must-use plugin or from `after_setup_theme` at a later priority.

## Why the comment heading is filtered

`zactonz_lite_comment_form_heading` sets `title_reply_before` and `title_reply_after` to `h2`. Core
hardcodes `<h3 id="reply-title">` in `comment_form()`, and this theme's comments-title block is level
2. On a post with comments the order reads h1, h2, h3 and is correct. On a post with **no** comments
the comments title renders nothing, and that h3 lands directly under the post h1 — a heading-level
skip on what is the ordinary page of a new site.

If you replace the comment form, keep the reply heading at level 2 or the skip returns.
