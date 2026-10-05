# Sitemaps, SEO and metadata

## Sitemaps are core's job and the theme leaves them alone

WordPress has shipped XML sitemaps since 5.5. They are on by default at `/wp-sitemap.xml`, with
indexed sub-sitemaps for posts, pages, taxonomies, authors and any public custom post type.

Zactonz Lite does not touch them — it neither disables, extends, nor replaces them. Visit
`/wp-sitemap.xml` on a fresh install with this theme active and it is there.

To change what they contain, use core's own filters from a plugin:

| Filter | Use |
|---|---|
| `wp_sitemaps_enabled` | Turn the whole thing off |
| `wp_sitemaps_post_types` | Add or drop a post type |
| `wp_sitemaps_taxonomies` | Add or drop a taxonomy |
| `wp_sitemaps_posts_query_args` | Exclude individual posts |
| `wp_sitemaps_add_provider` | Register your own provider |

If an SEO plugin is active it usually replaces core's sitemap with its own. Nothing in this theme
interferes with that either way.

## What the theme does not emit, and why

A directory theme may not ship SEO features. The review handbook names "SEO options" as plugin
territory, alongside analytics, caching, contact forms and dashboard widgets. So:

- **No meta description.** This is the single reason Lighthouse SEO scores 92 rather than 100.
  Verified rather than assumed: GeneratePress, Neve and Twenty Twenty-Five score **exactly 92 with
  the same failure** on the same install. It is the block-theme ceiling, not a defect in this theme.
- **No Open Graph or Twitter cards.**
- **No JSON-LD.** Measured against `wp-themes.com`, which serves the real approved builds: **0 of
  the 10 most-installed directory themes ship JSON-LD.** Five use microdata attributes on existing
  markup instead. Emitting a graph from a theme also collides with whichever SEO plugin the site
  runs.
- **No `rel="prev/next"` pagination hints.** Google stopped using them in 2019.
- **No `remove_action( 'wp_head', … )` of any kind.** Theme Check reports every one as a REQUIRED
  failure — the generator tag, RSD link, shortlink, adjacent rel links and oEmbed discovery are all
  plugin territory. An earlier revision of this theme removed them and was deleted for that reason.
  `tools/lint.php` now fails the build if one comes back.

Install an SEO plugin for any of the above. That is the arrangement the directory intends.

## What the theme does own

These are markup and document-structure concerns, which are squarely a theme's job, and they were
audited across all six template types:

- `lang` and a viewport meta on every view.
- Exactly one `h1` per view, with **no heading-level skips** — including on a post with no comments,
  which is the case that is easy to miss.
- `<link rel="canonical">` on singular views only.
- `noindex, follow` on search results.
- One each of `header`, `nav`, `main` and `footer` landmarks.
- Every image carries `alt`; no vague link text.
- The feed link core generates, kept.
- `<time datetime>` with absolute dates.

Measured result: Lighthouse SEO 92 (the ceiling above), accessibility 100, best practices 100,
performance 99–100, with axe reporting zero violations on home, single, 404 and search, and zero
again on the dark variation.

## `<article>` on single posts

It is absent, and that is the block-theme norm rather than a gap — `<article>` is missing on a single
post in GeneratePress, Neve and Twenty Twenty-Five too. Core's `post-content` block does not wrap
itself in one. If you want it, wrap `wp:post-content` in a Group with `tagName: "article"` in
`templates/single.html`.
