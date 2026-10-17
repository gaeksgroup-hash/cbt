#!/usr/bin/env bash

set -u

APP_ROOT="/home/u922552590/domains/gaeks.com/cbt-app"
WEB_ROOT="/home/u922552590/domains/gaeks.com/public_html/cbt"
STAGE_ROOT="$(cd -- "$(dirname -- "$0")" && pwd)"
PHP_BIN="/opt/alt/php83/usr/bin/php"
ENTITLEMENT_SECRET_FILE="/home/u922552590/domains/gaeks.com/cbt-entitlement-secret"
STAMP="$(date +%Y%m%d-%H%M%S)"
DEPLOY_STATUS="failed"

finish() {
    if [ -f "$APP_ROOT/artisan" ]; then
        "$PHP_BIN" "$APP_ROOT/artisan" up >/dev/null 2>&1 || true
    fi

    echo "deploy_status=$DEPLOY_STATUS"

    if [ "$DEPLOY_STATUS" = "ok" ]; then
        rm -f "$STAGE_ROOT/r.zip"
        rm -rf "$STAGE_ROOT/release" "$STAGE_ROOT/private"
    fi
}

trap finish EXIT
set -e

mkdir -p "$APP_ROOT/database" "$APP_ROOT/storage/app/private/import"

if [ -f "$APP_ROOT/database/database.sqlite" ]; then
    cp -p "$APP_ROOT/database/database.sqlite" "$APP_ROOT/database/database.sqlite.backup-$STAMP"
fi

if [ -f "$APP_ROOT/.env" ]; then
    cp -p "$APP_ROOT/.env" "$APP_ROOT/.env.backup-$STAMP"
fi

if [ -r "$ENTITLEMENT_SECRET_FILE" ]; then
    ENTITLEMENT_SECRET="$(tr -d '\r\n' < "$ENTITLEMENT_SECRET_FILE")"
    if [ "${#ENTITLEMENT_SECRET}" -lt 48 ]; then
        echo "Shared GDP/CBT entitlement secret is invalid." >&2
        exit 1
    fi
    sed -i '/^GDP_ENTITLEMENT_SECRET=/d' "$APP_ROOT/.env"
    printf 'GDP_ENTITLEMENT_SECRET=%s\n' "$ENTITLEMENT_SECRET" >> "$APP_ROOT/.env"
fi

if ! grep -q '^GDP_ENTITLEMENT_SECRET=.' "$APP_ROOT/.env"; then
    echo "GDP_ENTITLEMENT_SECRET is missing; configure the shared GDP/CBT secret before deployment." >&2
    exit 1
fi

if [ -f "$APP_ROOT/artisan" ]; then
    "$PHP_BIN" "$APP_ROOT/artisan" down --retry=15 || true
fi

rm -f "$STAGE_ROOT/release/bootstrap/cache/"*.php
cp -a "$STAGE_ROOT/release/." "$APP_ROOT/"
cp -p "$STAGE_ROOT/private/sak_bank_v1.json" "$APP_ROOT/storage/app/private/import/sak_bank_v1.json"

if ! grep -q '^CBT_TOKEN_PEPPER=.' "$APP_ROOT/.env"; then
    sed -i '/^CBT_TOKEN_PEPPER=/d' "$APP_ROOT/.env"
    TOKEN_PEPPER="$($PHP_BIN -r 'echo bin2hex(random_bytes(32));')"
    printf 'CBT_TOKEN_PEPPER=%s\n' "$TOKEN_PEPPER" >> "$APP_ROOT/.env"
fi

if grep -q '^CBT_PREVIEW_ONLY=' "$APP_ROOT/.env"; then
    sed -i 's/^CBT_PREVIEW_ONLY=.*/CBT_PREVIEW_ONLY=false/' "$APP_ROOT/.env"
else
    printf '\nCBT_PREVIEW_ONLY=false\n' >> "$APP_ROOT/.env"
fi

if grep -q '^CBT_ALLOW_PRE_GENERATED_CANDIDATES=' "$APP_ROOT/.env"; then
    sed -i 's/^CBT_ALLOW_PRE_GENERATED_CANDIDATES=.*/CBT_ALLOW_PRE_GENERATED_CANDIDATES=false/' "$APP_ROOT/.env"
else
    printf 'CBT_ALLOW_PRE_GENERATED_CANDIDATES=false\n' >> "$APP_ROOT/.env"
fi

if grep -q '^GDP_ENTITLEMENT_URL=' "$APP_ROOT/.env"; then
    sed -i 's|^GDP_ENTITLEMENT_URL=.*|GDP_ENTITLEMENT_URL=https://gdp.gaeks.com/api/cbt-entitlements.php|' "$APP_ROOT/.env"
else
    printf 'GDP_ENTITLEMENT_URL=https://gdp.gaeks.com/api/cbt-entitlements.php\n' >> "$APP_ROOT/.env"
fi

cd "$APP_ROOT"
"$PHP_BIN" artisan config:clear
"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan db:seed --class=CandidateUserSeeder --force
"$PHP_BIN" artisan db:seed --class=SakExamSeeder --force
"$PHP_BIN" artisan cbt:import-sak storage/app/private/import/sak_bank_v1.json
"$PHP_BIN" artisan cbt:validate-bank SAK
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

mkdir -p "$WEB_ROOT/build"
cp -a "$APP_ROOT/public/build/." "$WEB_ROOT/build/"
cp -p "$APP_ROOT/deploy/hostinger/index.php" "$WEB_ROOT/index.php"

CANDIDATE_COUNT="$($PHP_BIN -r '$db=new PDO("sqlite:/home/u922552590/domains/gaeks.com/cbt-app/database/database.sqlite"); echo $db->query("SELECT COUNT(*) FROM candidate_users")->fetchColumn();')"
QUESTION_COUNT="$($PHP_BIN -r '$db=new PDO("sqlite:/home/u922552590/domains/gaeks.com/cbt-app/database/database.sqlite"); echo $db->query("SELECT COUNT(*) FROM questions")->fetchColumn();')"

echo "candidate_users=$CANDIDATE_COUNT"
echo "questions=$QUESTION_COUNT"
DEPLOY_STATUS="ok"
