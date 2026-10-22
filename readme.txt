=== Zactonz Lite ===

Contributors: zactonz
Requires at least: 6.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.2
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, one-column, wide-blocks, block-patterns, block-styles, custom-colors, custom-logo, editor-style, featured-images, full-site-editing, style-variations, template-editing

A featherweight block theme that adds no avoidable weight to your pages.

== Description ==

Zactonz Lite is a block theme built around one measurable goal: adding as little as possible to
the page. It registers its stylesheet with a filesystem path so WordPress inlines it, which means
the theme contributes no render-blocking stylesheet request at all. Its default style uses the
reader's own system font, so no web font is downloaded unless you choose a style variation that
uses one.

Eight templates cover a blog or a small site, including a wide page template, with a header and
footer template part, five patterns to insert and three style variations: Dark, Editorial and Ink.
Editorial uses a bundled Instrument Serif for headings; the font is fetched only when that
variation is active.

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

Placeholder image used by the "Image beside text" pattern, assets/images/placeholder.webp
Created for this theme by Zactonz Technologies, licensed GPL-2.0-or-later, same as the theme.

== Upgrade Notice ==

= 1.0.2 =
Fixes the Image beside text pattern, which reported invalid content in the editor, and restores scrolling on wide tables.

= 1.0.1 =
Fixes the navigation block's overlay menu, keeps wide content inside its own box, and removes the speculative loading module, which belonged to a plugin. Adds a translation template.

== Changelog ==

= 1.0.2 =
* Image beside text pattern is valid in the editor again: core's media-text block saves only src and alt on its image, so the width and height attributes are gone and the proportions are reserved in CSS instead.
* Wide tables scroll again. The overflow guard added in 1.0.1 put display:block on every table, which made it shrink to fit and left core's own scrolling wrapper with nothing to scroll.
* Add the file-level documentation blocks the two include files were missing.
* Use _x() rather than esc_attr_x() for a block attribute in the posts list pattern.

= 1.0.1 =
* Keep the core navigation block stylesheet instead of replacing it, so a navigation block with the overlay menu enabled works as core intends.
* Remove the speculative loading module; choosing a prerender mode belongs to the site owner, through the Speculative Loading plugin.
* Give the root padding a unit, so core rules that clamp it no longer drop the whole declaration.
* Keep wide content inside its own box: preformatted text, tables, embeds, classic captions and unbreakable strings.
* Ship a languages folder with a translation template.
* Drop the sticky-post tag, which described styling the theme does not have.
* Hero pattern heading starts at level 2, so inserting it cannot produce a second H1.
* Give the Image beside text pattern a picture to preview with.
* Document every function, and rename a filter that shared its callback's name.

= 1.0.0 =
* Initial release.
