#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$root"

name="$(sed -n 's/^Theme Name:[[:space:]]*//p' style.css | head -1 | tr -d '\r')"
slug="$(printf '%s' "$name" | tr '[:upper:]' '[:lower:]' | sed -E 's/[^a-z0-9]+/-/g; s/^-//; s/-$//')"
dir_slug="$(basename "$root")"

if [ "$slug" != "$dir_slug" ]; then
	printf 'slug mismatch: Theme Name "%s" derives "%s" but directory is "%s"\n' "$name" "$slug" "$dir_slug" >&2
	exit 1
fi

domain="$(sed -n 's/^Text Domain:[[:space:]]*//p' style.css | head -1 | tr -d '\r')"
if [ "$domain" != "$slug" ]; then
	printf 'text domain "%s" does not match slug "%s"\n' "$domain" "$slug" >&2
	exit 1
fi

php tools/build-css.php >/dev/null

dist="$root/dist"
rm -rf "$dist"
mkdir -p "$dist/$slug"

rsync -a \
	--exclude '.git' \
	--exclude '.github' \
	--exclude 'dist' \
	--exclude 'tools' \
	--exclude 'node_modules' \
	--exclude '.DS_Store' \
	--exclude '*.zip' \
	./ "$dist/$slug/"

( cd "$dist" && zip -qr "$slug.zip" "$slug" )

printf 'packaged %s (%s)\n' "$slug" "$(du -h "$dist/$slug.zip" | cut -f1)"
printf 'files: %s\n' "$(find "$dist/$slug" -type f | wc -l | tr -d ' ')"
