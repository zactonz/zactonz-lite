#!/usr/bin/env bash
set -uo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$root"
status=0

echo "== php -l =="
while IFS= read -r f; do
	if ! out=$(php -l "$f" 2>&1); then
		echo "FAIL  $out"
		status=1
	fi
done < <(find . -name '*.php' -not -path './dist/*' -not -path './.git/*')
[ "$status" -eq 0 ] && echo "  all PHP files parse"

echo
echo "== css build is current =="
before=$(find assets/css -name '*.min.css' -exec md5 -q {} \; 2>/dev/null | sort | md5 -q)
php tools/build-css.php >/dev/null
after=$(find assets/css -name '*.min.css' -exec md5 -q {} \; 2>/dev/null | sort | md5 -q)
if [ "$before" != "$after" ]; then
	echo "FAIL  minified CSS was stale; tools/build-css.php has regenerated it"
	status=1
else
	echo "  minified CSS is up to date"
fi

echo
echo "== theme rules =="
if ! php tools/lint.php; then
	status=1
fi

exit "$status"
