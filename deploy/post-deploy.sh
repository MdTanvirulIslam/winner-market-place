#!/bin/sh
# Post-deploy tasks, executed ON THE SERVER by the temporary cron the deploy
# workflow schedules (see .github/workflows/deploy.yml). Everything here must
# be idempotent — the cron may fire more than once before it is removed.

set -x

PHP_BIN=$(command -v php || echo /usr/local/bin/php)

"$PHP_BIN" artisan migrate --force

# Product images live on the public disk and are served through the
# public/storage symlink. Recreate it every deploy: a stale or dangling link
# (or a real directory left behind by an extract) breaks every image with a
# 403. A real directory is moved aside, never deleted.
if [ -d public/storage ] && [ ! -L public/storage ]; then
    mv public/storage "public/storage-replaced-$(date +%s)"
fi
"$PHP_BIN" artisan storage:link --force

# Files that once lived in a real public/storage directory (moved aside
# above on an earlier deploy) belong on the public disk — merge them back
# without overwriting anything newer.
for replaced in public/storage-replaced-*; do
    [ -d "$replaced" ] || continue
    cp -Rn "$replaced"/. storage/app/public/ 2>/dev/null || true
done

# The web server serves the linked files directly, so they must be
# world-readable (PHP writes them as the account user).
chmod o+x storage storage/app
chmod -R o+rX storage/app/public

"$PHP_BIN" artisan config:clear

# Screenshots uploaded before server-side processing existed get brought to
# the standard gallery size. Idempotent — already-correct files are skipped.
"$PHP_BIN" artisan screenshots:normalize

# Diagnostics for the deploy log (readable in cPanel: storage/logs/deploy-*).
# Note: these are the CLI values — the web values come from public/.user.ini,
# which the host applies to the web SAPI only.
"$PHP_BIN" -r 'echo "cli upload_max_filesize=", ini_get("upload_max_filesize"), " post_max_size=", ini_get("post_max_size"), PHP_EOL;'
ls -ld storage/app/public public/storage public/.user.ini
