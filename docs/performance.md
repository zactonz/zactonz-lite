# Performance

The README carries the comparison tables. This page is about the mechanisms behind them and how to
verify or change them.

## Where the weight actually went

Two decisions account for most of the difference, and both are filterable.

**The navigation stylesheet.** Core's is 20,776 bytes; the theme's replacement is 1,628. That single
substitution saved 19,148 bytes per page — at the time, 28% of total page weight. See
[Navigation and menus](navigation.md).

**`overlayMenu: "never"`.** Removes the modal markup, the Interactivity API attributes and all
front-end JavaScript of the theme's own.

Everything else — the spacing scale, on-demand block assets, no bundled icon font, no third-party
runtime resource at all — is ordinary discipline rather than a trick.

## Fonts

A bundled font is preloaded **only when the active global styles reference its slug**. The default
variation references none, emits zero preloads and downloads nothing; Editorial emits exactly one
and pulls 15,040 bytes.

That works because declaring `@font-face` costs only the CSS. The browser fetches the file when text
uses the family, so an unused declaration is nearly free.

Two practical notes for anyone swapping the font:

- Google Fonts serves pre-subset woff2, so no local `fonttools` step is needed.
- Request without the `opsz` axis. Newsreader is 129 KB with it and 58 KB without; Instrument Serif
  is 15 KB.

Turn preloading off with `zactonz_lite_enable_font_preload`. See [Extension points](extending.md).


## Reproducing the numbers

```bash
php tools/measure.php    http://localhost:8734/
php tools/benchmark.php              # switches themes and restores
```

`benchmark.php` has an image-heavy path and an optional plugin fixture:

```bash
ZL_WITH_PLUGIN=1 php tools/benchmark.php
```

which enqueues jQuery plus one external script, to show what the margin looks like on a site that
is not bare.

## Two things the measurements get right that are easy to get wrong

**Scripts loaded from inline blocks count.** Core's `print_emoji_detection_script()` puts 3,936
bytes inline in every page, and that inline block pulls `wp-emoji-release.min.js`, a further 22,910
bytes. A measurement that greps for `<script src=…>` misses all 26.8 KB of it. `measure.php` scans
inline script bodies for `.js` URLs for exactly this reason.

The theme may not remove it — `remove_action( 'wp_head', 'print_emoji_detection_script' )` is a
REQUIRED Theme Check failure. Every comparator carries the identical script, so it raises all four
themes equally; counting it narrows the lead over GeneratePress from 47% to 38%, and the README
reports the counted figures.

**Images are excluded from `total_bytes` on purpose.** They are content, identical whichever theme
renders them, and `src` is WordPress's largest `srcset` candidate rather than what a browser
fetches — Chrome took the 768w variant at 29,996 bytes where `src` is 89,645. Including them would
measure the fixture, not the theme. They are counted separately, eager apart from lazy.

## Measured results

Lighthouse, local harness, three views:

| View | Perf | A11y | Best practices | SEO |
|---|---|---|---|---|
| Home, mobile | 100 | 100 | 100 | 92 |
| Home, desktop | 100 | 100 | 100 | 92 |
| Single post, mobile | 100 | 100 | 100 | 92 |

Core Web Vitals in the good band throughout: mobile FCP 1.1s / LCP 1.8s, desktop 0.3s / 0.6s,
**TBT 0ms and CLS 0 everywhere**. `unused-css-rules` and `unminified-css` both pass.

SEO 92 is the ceiling for a directory theme, not a defect — see
[Sitemaps, SEO and metadata](sitemap.md).

## Cache eligibility

Nothing in the theme writes cookies, varies output per visitor, or defeats a full-page cache. A site
behind Cloudflare, Varnish or a caching plugin can serve these pages from cache without exceptions
for the theme.

## Speculative loading is not the theme's business

`inc/performance.php` configured `wp_speculation_rules_configuration` until 1.0.1. It was removed:
choosing a prerender mode is a site-wide decision with no presentational component, it is owned by
the Speculative Loading plugin, and because a theme loads after plugins the theme's callback
silently overrode whatever the site owner had saved there.
