# Zactonz Lite

A WordPress block theme built around one measurable goal: adding as little as possible to the page.
This file carries the explanation, because the shipped code carries no comments.

**Developer documentation:** [docs/](docs/README.md) — extension points, navigation, templates and
patterns, sidebars, sitemaps and SEO, styles and variations, performance.

## What it is

A full block theme (FSE). `theme.json` is the single source of style truth. The theme ships **no
render-blocking stylesheet request**: its CSS is registered with filesystem `path` data, so
`wp_maybe_inline_styles()` inlines it. The default style uses the reader's system font stack, so no
web font is downloaded unless a style variation that uses one is active.

PHP exists only for asset registration and the speculation-rules configuration. It never renders.

## Measured

Same install, same content (WordPress Theme Unit Test data), same URLs, same method. Bytes are
uncompressed; total is the document plus every CSS, JS and font subresource, **including the
scripts WordPress loads from an inline block rather than a `src` attribute**. Reproduce with
`php tools/benchmark.php`.

| Home | total bytes | CSS requests | JS requests | DOM elements |
|---|---|---|---|---|
| **Zactonz Lite** | **78,911** | **0** | **0** (+2) | **134** |
| GeneratePress 3.6.1 | 126,757 | 1 | 1 (+2) | 392 |
| Neve 4.2.15 | 134,576 | 1 | 2 (+2) | 313 |
| Twenty Twenty-Five 1.5 | 280,916 | 2 | 2 (+3) | 1,083 |

| Single post, 19 comments | total bytes | CSS requests | JS requests | DOM elements |
|---|---|---|---|---|
| **Zactonz Lite** | **102,648** | **0** | 1 (+2) | **397** |
| GeneratePress 3.6.1 | 133,917 | 2 | 2 (+2) | 544 |
| Neve 4.2.15 | 149,959 | 1 | 3 (+2) | 545 |
| Twenty Twenty-Five 1.5 | 186,708 | 1 | 2 (+3) | 521 |

| Post with 5 images | total bytes | CSS requests | JS requests | DOM elements |
|---|---|---|---|---|
| **Zactonz Lite** | **100,666** | **0** | 1 (+2) | **210** |
| GeneratePress 3.6.1 | 135,504 | 2 | 2 (+2) | 355 |
| Neve 4.2.15 | 151,599 | 1 | 3 (+2) | 353 |
| Twenty Twenty-Five 1.5 | 182,717 | 1 | 2 (+3) | 324 |

The number in brackets is scripts loaded at runtime by an inline block. Every theme carries the
same two, core's emoji detection and its loader, about 25.7 KB: a theme in the directory is not
allowed to remove them. Image bytes are excluded from these totals, because the images are content
and identical for every theme; `tools/benchmark.php` prints them separately.

A theme's share of a page shrinks as a site adds plugins. With one fixture plugin active that
enqueues jQuery and one script of its own, `ZL_WITH_PLUGIN=1 php tools/benchmark.php`, the home
page goes to 181,869 bytes here and 229,668 for GeneratePress: still lighter, by 21% rather
than 38%.

Measured on WordPress 7.1.2, PHP 8.5.5, MariaDB 12.3.3.

### What these numbers are not

They are lab measurements of what the theme itself contributes on one controlled install. They say
nothing about Core Web Vitals in the field or about search rankings, both of which depend on
hosting, images, content and other plugins. No theme can promise those, and this one does not.

## Where the weight went

Two decisions account for most of the difference:

1. **The navigation stylesheet is replaced.** Core's `wp-block-navigation` stylesheet is 20,776
   bytes, of which the responsive overlay and submenu variants are the bulk. The theme re-registers
   that handle to `assets/css/navigation.css` (1,628 bytes), which covers a horizontal menu and
   submenus. Filter `zactonz_lite_slim_navigation_style` to `false` to keep core's.
2. **The header menu does not use the overlay.** `overlayMenu` is `never`, so there is no modal
   markup, no Interactivity API attributes and no JavaScript of our own on the front page. The one
   script that remains is core's emoji detection, which a directory theme may not remove. The menu wraps on
   narrow screens. A long menu will wrap to several rows; sites that need an overlay should set it
   in the editor and restore core's navigation CSS with the filter above.

The remaining bulk is core's own global-styles output, which scales with the number of presets in
`theme.json`. Those presets are the design system the editor exposes, so they were left intact
rather than trimmed for a few per cent.

## Filters

| Filter | Default | Effect |
|---|---|---|
| `zactonz_lite_load_setup` | `true` | Load `inc/setup.php` |
| `zactonz_lite_load_assets` | `true` | Load `inc/assets.php` |
| `zactonz_lite_load_performance` | `true` | Load `inc/performance.php` |
| `zactonz_lite_slim_navigation_style` | `true` | Replace core's navigation stylesheet |
| `zactonz_lite_preload_fonts` | `true` | Preload bundled fonts that the active styles use |
| `zactonz_lite_speculation` | config array | Final speculation-rules configuration |
| `zactonz_lite_speculation_exclude_paths` | cart, checkout, account paths | Paths excluded from speculative loading |

Each one, with signatures, worked examples and the cases where it is not called, is in
[docs/extending.md](docs/extending.md).

## Speculative loading

Core 6.8+ ships the Speculation Rules API and defaults to `prefetch` / `conservative`. The theme
raises this to `prerender` / `moderate`, which makes in-site navigation feel near-instant. It backs
off to core's default when WooCommerce or Easy Digital Downloads is active, because prerendering
issues real requests and that is unsafe for carts. Commerce paths are excluded regardless.

This improves perceived navigation speed. It does not change first-load LCP.

## Fonts

The default style uses a system font stack: no download, no FOUT, no layout shift.

`theme.json` declares an `Instrument Serif` family with a `fontFace`, which costs only the
`@font-face` CSS; browsers fetch a font file only when text actually uses that family. Only the
Editorial variation uses it, so only Editorial pays the 15,040 bytes.

`inc/assets.php` preloads a bundled font **only when the resolved global styles actually reference
its family slug**, via `wp_get_global_styles()`. Verified: default variation emits no preload and
downloads no font; Editorial emits one preload with `fetchpriority="high"`.

The font file carries its version in its filename rather than a `?ver=` query string, because some
CDN configurations strip query strings and would then serve a stale asset.

## Cache eligibility

The theme is built so a page-cache plugin or a CDN can cache its output, and `tools/lint.php`
enforces it rather than the readme claiming it. The theme sets no cookie, starts no session, emits
no front-end nonce, never branches front-end output on `is_user_logged_in()`, and uses absolute
dates rather than `human_time_diff()` so a cached page never shows a stale "2 hours ago".

Note that Cloudflare does not cache HTML by default; it caches by file extension and HTML is not
among them. Cache-safe markup keeps a page *eligible* — notably by never sending `Set-Cookie`,
which is an explicit bypass — but enabling HTML caching is a zone configuration the site owner makes.

## What this theme deliberately does not do

- **No structured data.** None of the ten most-installed directory themes ship JSON-LD; emitting a
  graph from a theme conflicts with whichever SEO plugin the site runs.
- **No meta descriptions, Open Graph or Twitter cards.** Plugin territory.
- **No caching.** The WordPress.org review handbook lists "Resource caching" as plugin territory.
- **No settings screen.** The same list names "Dashboard widgets in the admin area".
- **No `remove_action( 'wp_head', … )`.** Theme Check reports every one of these as a REQUIRED
  failure. An earlier revision removed the generator tag, RSD link, shortlink, oEmbed discovery and
  emoji script; it was deleted for this reason.
- **No `rel="prev/next"` pagination hints.** Google stopped using them in 2019.

What the theme does own: a correct document outline with exactly one `h1` per view, named landmark
elements, navigation that is crawlable without JavaScript, `<time datetime>` with absolute dates,
`alt` discipline in every shipped pattern, and as few bytes as possible.

## Comment policy and its three exemptions

Shipped code carries no comments. Three exemptions are unavoidable and are required by WordPress:

1. Block templates, parts and patterns **are** HTML comments (`<!-- wp:group -->`).
2. Every pattern file needs a docblock header, and `style.css` needs its theme header.
3. Translator comments (`/* translators: … */`) are mandated by the i18n standards wherever a
   translated string takes a placeholder.

Everything else is explained here instead.

## Working on it

```bash
php tools/build-css.php     # *.un-compressed.css -> *.css
bash tools/lint.sh          # the single gate
php tools/measure.php URL   # one page's weight
php tools/benchmark.php     # four-theme comparison, switches themes and restores
bash tools/package.sh       # dist/zactonz-lite.zip, excludes tools/
```

`tools/lint.sh` runs `php -l`, checks the CSS build is current, and then `tools/lint.php`, which
covers: required files, JSON validity, block-attribute JSON, text domain, `zactonz_lite_` prefixing,
the `wp_head` removal ban, cache-eligibility, jQuery, remote resources, minified CSS having a
readable source, version agreement across `style.css`/`functions.php`/`readme.txt`, theme tags
being ones WordPress recognises, and attribution.

### Release gate

Theme Check must run against the **packaged** tree, never the working copy — checking the working
copy reports `tools/` as errors (`shell_exec`, `curl_init`, `file_put_contents`) for files that do
not ship.

```bash
bash tools/package.sh
# replace the themes/ symlink with dist/zactonz-lite, then:
wp theme-check run zactonz-lite --format=json
```

Current status: **0 REQUIRED, 0 WARNING, 0 RECOMMENDED**, one INFO confirming the text domain.

### Local harness gotchas

- Theme pattern files are cached in a **site** transient. `wp transient delete --all` does not clear
  it; use `--network`, or set `WP_DEVELOPMENT_MODE = 'theme'` in `wp-config.php`.
- A `wp_global_styles` post without its `wp_theme` term cannot be found by the resolver, which then
  creates another one on every write. If style variations appear not to apply, check for duplicate
  `wp-global-styles-zactonz-lite-N` posts.

## Licence

GPLv2 or later. Bundled Instrument Serif is SIL OFL 1.1; its licence is in
`assets/fonts/InstrumentSerif-OFL.txt` and credited in `readme.txt`.
