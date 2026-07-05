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

# The web server serves the linked files directly, so they must be
# world-readable (PHP writes them as the account user).
chmod o+x storage storage/app
chmod -R o+rX storage/app/public

"$PHP_BIN" artisan config:clear
