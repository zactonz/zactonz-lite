=== Zactonz Lite ===

Contributors: zactonz
Requires at least: 6.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, one-column, two-columns, wide-blocks, block-patterns, block-styles, custom-colors, custom-logo, editor-style, featured-images, full-site-editing, style-variations, sticky-post, template-editing

A featherweight block theme that adds no avoidable weight to your pages.

== Description ==

Zactonz Lite is a block theme built around one measurable goal: adding as little as possible to
the page. It registers its stylesheet with a filesystem path so WordPress inlines it, which means
the theme contributes no render-blocking stylesheet request at all. Its default style uses the
reader's own system font, so no web font is downloaded unless you choose a style variation that
uses one.

Seven templates cover a blog or a small site, with a header and footer template part, seven
patterns and three style variations: Dark, Editorial and Ink. Editorial uses a bundled Instrument
Serif for headings; the font is fetched only when that variation is active.

Structured data, meta tags, sitemaps and caching are deliberately left to plugins that specialise
in them. The theme stays out of their way and does the part a theme can legitimately own: clean
semantics, one H1 per view, named landmarks, crawlable navigation that needs no JavaScript, and
as few bytes as possible.

== Frequently Asked Questions ==

= Does this theme do SEO? =

It does the structural part: a correct document outline, one H1 per view, landmark elements,
accessible navigation that works without JavaScript, and fast pages. It does not output meta
descriptions, Open Graph tags or structured data, because those belong to an SEO plugin and
emitting them from a theme conflicts with whichever plugin you use.

= Does this theme cache pages? =

No. Caching belongs to a caching plugin. The theme is built to be cache friendly: it sets no
cookie, starts no session, emits no nonce on the front end, and uses absolute dates rather than
relative ones, so a cached page never shows a stale "2 hours ago".

= Why is there no settings page? =

Everything is in the Site Editor, where WordPress expects theme settings to live.

== Copyright ==

Zactonz Lite WordPress Theme, (C) 2026 Zactonz Technologies.
Zactonz Lite is distributed under the terms of the GNU General Public License v2 or later.

This theme bundles the following third-party resources:

Instrument Serif
Copyright 2022 The Instrument Serif Project Authors
License: SIL Open Font License, 1.1
Source: https://github.com/Instrument/instrument-serif
Licence text: assets/fonts/InstrumentSerif-OFL.txt

== Changelog ==

= 1.0.0 =
* Initial release.
