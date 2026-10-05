# Navigation and menus

## There is no classic menu

This is a block theme. `register_nav_menus()` is not called and `wp_nav_menu()` is never used.
Navigation is the `core/navigation` block, edited in **Appearance → Editor**, and a menu is stored as
a `wp_navigation` post rather than in the classic menu tables.

The `custom-menu` theme tag no longer exists in `get_theme_feature_list()`, which is the directory's
way of saying the same thing.

## How the header is built

`parts/header.html` contains one line:

```html
<!-- wp:pattern {"slug":"zactonz-lite/header"} /-->
```

The real markup lives in `patterns/header.php`, which is registered with
`Block Types: core/template-part/header` and `Inserter: no`. That indirection is deliberate: a
pattern can be swapped or filtered, and the part stays a stable reference.

The navigation block inside it:

```html
<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"}} /-->
```

## `overlayMenu: "never"` and what it buys

With the default `"mobile"`, core renders a hamburger button, a modal container, the open/close
markup and the Interactivity API attributes that drive it — and enqueues the script that makes it
work. Setting `"never"` removes all of that. **The front page ships no JavaScript of the theme's
own**, and the menu is plain anchor markup a crawler reads without executing anything.

At narrow widths the items wrap onto a second line instead of collapsing behind a button. Verified at
375px: all four items visible, no horizontal page scroll.

The trade-off is real. A long menu wraps to several lines on a phone. If you need an overlay, change
it and restore core's stylesheet — the next section explains why both steps are needed.

## The navigation stylesheet substitution

Core's `wp-block-navigation` stylesheet is **20,776 bytes**, and most of it styles things this header
never renders: the responsive overlay, the open and closed states, submenu positioning and the
several layout variants. The theme replaces it with `assets/css/navigation.css`, **1,628 bytes**,
covering only what it actually uses — a saving of 19,148 bytes on every page.

It is a handle substitution, not a dequeue:

```php
wp_deregister_style( 'wp-block-navigation' );
wp_register_style( 'wp-block-navigation', ZACTONZ_LITE_URI . '/assets/css/navigation.css', array(), ZACTONZ_LITE_VERSION );
```

Keeping the handle name matters. Anything that depends on `wp-block-navigation` still resolves, and
core's on-demand block-asset loading still decides when to print it.

The proof that it works is not the file size — it is that none of core's nav-only selectors
(`responsive-container-open`, `has-modal-open`, `is-menu-open`) appear in the rendered HTML.

### If you change the header

Restore core's stylesheet whenever your navigation uses an overlay or submenus, or they will render
unstyled:

```php
add_filter( 'zactonz_lite_slim_navigation_style', '__return_false' );
```

From a must-use plugin, because the filter is read while the theme loads.

### If you extend the slim stylesheet instead

Edit `assets/css/navigation.un-compressed.css`, then rebuild:

```bash
php tools/build-css.php
```

The minified file and its readable source must both ship — the directory requires it, and
`tools/lint.php` fails if they drift.

## Submenus

The slim stylesheet does not carry core's submenu rules. If you add a submenu, take the
`zactonz_lite_slim_navigation_style` route above rather than copying fragments of core's CSS, which
changes between releases.

## Accessibility

A skip link is present, focus is visible and real — `solid 2px rgb(31,92,77)` at 2px offset, matched
to `:focus-visible` — and there are no positive `tabindex` values. Measured contrast on the focus
ring is 7.79:1 against a 3:1 requirement.

When testing focus, press a real Tab key. A scripted `el.focus()` followed by `getComputedStyle`
reports `outlineStyle: none`, because `:focus-visible` does not reliably apply to programmatic
focus. That reads as a missing focus indicator and is not one.
