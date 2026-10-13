#!/usr/bin/env bash
#
# Compila Tailwind usando el CLI standalone (no requiere Node).
# Uso:
#   bin/build-css.sh          # compila minificado
#   bin/build-css.sh watch    # recompila al guardar
#
set -euo pipefail

VERSION="v4.3.3"
BIN="tools/tailwindcss"
URL="https://github.com/tailwindlabs/tailwindcss/releases/download/${VERSION}/tailwindcss-linux-x64"
INPUT="resources/css/app.css"
OUTPUT="assets/css/app.css"

if [ ! -x "$BIN" ]; then
    echo "Descargando Tailwind CLI ${VERSION}..."
    mkdir -p tools
    curl -sL -o "$BIN" "$URL"
    chmod +x "$BIN"
fi

if [ "${1:-build}" = "watch" ]; then
    "$BIN" -i "$INPUT" -o "$OUTPUT" --watch
else
    "$BIN" -i "$INPUT" -o "$OUTPUT" --minify
fi
