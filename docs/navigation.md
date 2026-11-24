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

The trade-off is real. A long menu wraps to several lines on a phone. If you need an overlay, set
`overlayMenu` back to `"mobile"` on the navigation block. Core's own stylesheet and scripts are
present, so the overlay works with no further change — see **The core stylesheet stays** below.

## Submenus

Submenus render with core's own rules. Anything the theme wants on top of them belongs in
`assets/css/main.css` with a more specific selector, rather than a copy of core's CSS, which changes
between releases.

## Accessibility

A skip link is present, focus is visible and real — `solid 2px rgb(31,92,77)` at 2px offset, matched
to `:focus-visible` — and there are no positive `tabindex` values. Measured contrast on the focus
ring is 7.79:1 against a 3:1 requirement.

When testing focus, press a real Tab key. A scripted `el.focus()` followed by `getComputedStyle`
reports `outlineStyle: none`, because `:focus-visible` does not reliably apply to programmatic
focus. That reads as a missing focus indicator and is not one.

## The core stylesheet stays

Earlier releases deregistered `wp-block-navigation` and substituted a smaller rewrite. That is gone
as of 1.0.1. The replacement never covered the responsive overlay, so a navigation block a user
inserted with the mobile menu enabled rendered without its container or its open and close buttons.
Core's stylesheet is left alone; anything the theme wants on top goes in `assets/css/main.css` with
a more specific selector.
