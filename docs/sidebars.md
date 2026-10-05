# Sidebars and widget areas

## There are none, and that is not an omission

Zactonz Lite registers no sidebars. `register_sidebar()` is never called, there is no
`dynamic_sidebar()`, and **Appearance → Widgets** does not appear.

That is how block themes work. When a block theme is active WordPress replaces the widgets screen
with the site editor, and the job widgets used to do — putting a column of reusable content beside
the main content — is now done with blocks in a template.

The directory's tag list reflects this: `footer-widgets` is still a valid tag, but `custom-menu` and
`threaded-comments` are not, and this theme declares none of the three.

## How to get a sidebar anyway

### A column in one template

The smallest version. Edit the template in **Appearance → Editor**, wrap the content in a Columns
block, and put whatever you want beside it. Nothing in the theme needs changing.

In markup, a two-column single post looks like:

```html
<!-- wp:columns -->
<div class="wp-block-columns">
	<!-- wp:column {"width":"66.66%"} -->
	<div class="wp-block-column" style="flex-basis:66.66%">
		<!-- wp:post-content /-->
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"width":"33.33%"} -->
	<div class="wp-block-column" style="flex-basis:33.33%">
		<!-- wp:latest-posts {"displayPostDate":true} /-->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
```

Columns stack on narrow screens by default, so the sidebar falls below the content on a phone with
no extra CSS.

### A reusable sidebar across several templates

Make it a template part, so one edit updates every template that uses it.

1. Create `parts/sidebar.html` with the blocks you want.
2. Declare it in `theme.json` so the editor labels it properly:

```json
"templateParts": [
	{ "name": "header",  "title": "Header",  "area": "header" },
	{ "name": "footer",  "title": "Footer",  "area": "footer" },
	{ "name": "sidebar", "title": "Sidebar", "area": "uncategorized" }
]
```

`area` accepts `header`, `footer` and `uncategorized`. There is no `sidebar` area in core, so a
sidebar part is `uncategorized` — that is correct, not a workaround.

3. Reference it from a template inside a column:

```html
<!-- wp:template-part {"slug":"sidebar","tagName":"aside"} /-->
```

`tagName: "aside"` renders a real `<aside>` landmark. Without it you get a `<div>`, which is a
genuine accessibility regression — the theme ships exactly one each of header, nav, main and footer,
and an unlabelled extra region undoes that.

### If you need widgets specifically

A plugin can call `register_sidebar()` while a block theme is active, and the Legacy Widget block
will render it. That is plugin territory by the directory's rules, so it cannot ship here — but
nothing in this theme blocks it.

## Why the theme does not ship one

A sidebar is bytes on every page. The measured figures in the README assume the shipped templates;
adding a column of recent posts and a search form adds markup, DOM nodes and often a query. That is
a reasonable trade for a site that wants it and a poor default for one that does not.
