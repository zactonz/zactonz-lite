# Templates, parts and patterns

## Templates

Eight files in `templates/`. Block themes resolve these through the standard template hierarchy, so a
child theme or a plugin filtering `get_block_templates` can override any of them.

| File | Used for |
|---|---|
| `index.html` | The fallback every block theme must ship |
| `home.html` | The posts page |
| `single.html` | A single post |
| `page.html` | A page |
| `page-wide.html` | A page, registered as a selectable custom template |
| `archive.html` | Category, tag, author, date |
| `search.html` | Search results |
| `404.html` | Not found |

`page-wide.html` is declared in `theme.json` under `customTemplates`, which is what puts **Page,
wide** in the template dropdown of the page editor:

```json
"customTemplates": [ { "name": "page-wide", "title": "Page, wide", "postTypes": [ "page" ] } ]
```

Add your own by dropping a file in `templates/` and adding an entry there. A template not listed in
`customTemplates` is still used by the hierarchy — the list only controls what an editor may pick by
hand.

## Template parts

Two, both declared in `theme.json` so the editor knows their area:

| Part | Area |
|---|---|
| `parts/header.html` | `header` |
| `parts/footer.html` | `footer` |

Each is a single `wp:pattern` reference. The markup lives in `patterns/header.php` and
`patterns/footer.php`, which are registered with `Block Types: core/template-part/header` and
`…/footer` and hidden from the inserter.

This is the standard directory arrangement: a user who resets a part in the editor gets the pattern
back, and translators get strings from a PHP file rather than from HTML.

## Patterns

Nine files in `patterns/`. Five appear in the inserter; four are structural and carry
`Inserter: no`.

| Slug | Title | In inserter | Categories |
|---|---|---|---|
| `zactonz-lite/hero` | Hero | yes | `zactonz-lite`, `banner`, `call-to-action` |
| `zactonz-lite/features-three-column` | Three columns of features | yes | `zactonz-lite`, `featured`, `columns` |
| `zactonz-lite/media-text` | Image beside text | yes | `zactonz-lite`, `featured` |
| `zactonz-lite/cta` | Call to action | yes | `zactonz-lite`, `call-to-action` |
| `zactonz-lite/posts-list` | Posts list | yes | `zactonz-lite`, `posts` |
| `zactonz-lite/header` | Header | no | `zactonz-lite` |
| `zactonz-lite/footer` | Footer | no | `zactonz-lite` |
| `zactonz-lite/not-found` | Page not found | no | `zactonz-lite` |
| `zactonz-lite/post-footer` | Post footer | no | `zactonz-lite` |

Patterns in `patterns/` are registered by core automatically from their docblock header — the theme
does not call `register_block_pattern()`.

The custom category is registered in `inc/setup.php`:

```php
register_block_pattern_category( 'zactonz-lite', array(
	'label'       => _x( 'Zactonz Lite', 'block pattern category', 'zactonz-lite' ),
	'description' => __( 'Patterns supplied by the Zactonz Lite theme.', 'zactonz-lite' ),
) );
```

### Adding one

Create `patterns/your-pattern.php` with the header block, then the markup:

```php
<?php
/**
 * Title: Your pattern
 * Slug: zactonz-lite/your-pattern
 * Categories: zactonz-lite
 * Description: One sentence.
 */
?>
<!-- wp:group --><div class="wp-block-group"></div><!-- /wp:group -->
```

Pattern files are cached in a **site** transient. `wp transient delete --all` does not clear it — use
`--network`, or set `WP_DEVELOPMENT_MODE = 'theme'` in `wp-config.php` while you work. This costs
more time than any other part of pattern development.

## Block styles

Two, registered in `inc/setup.php` with inline CSS so they cost nothing until used:

| Block | Name | Label | Effect |
|---|---|---|---|
| `core/group` | `zactonz-lite-boxed` | Boxed | Surface background, 0.375rem radius, spacing-40 padding |
| `core/image` | `zactonz-lite-framed` | Framed | 1px `accent-soft` border, 0.375rem radius |

Both are written against theme.json presets (`var(--wp--preset--color--surface)`), so they follow
whichever style variation is active rather than hardcoding a colour.

Remove one with `unregister_block_style( 'core/group', 'zactonz-lite-boxed' )` on `init` at a
priority above 10.

## What is not here

No PHP templates, no template tags, no `get_template_part()`. A block theme's `index.php` equivalent
is `templates/index.html`, and there is nothing to override with a copy in a child theme — you
override by shipping a file with the same name, which core resolves first.
