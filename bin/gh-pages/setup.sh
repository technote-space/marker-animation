#!/usr/bin/env bash

set -e

if [[ $# -lt 1 ]]; then
	exit 1
fi

SCRIPT_DIR=${1}
source ${SCRIPT_DIR}/variables.sh

yarn --cwd ${JS_DIR} install
yarn --cwd ${JS_DIR} build:g

cp -f ${JS_DIR}/gutenberg.min.js ${GH_PAGES_DIR}/index.min.js
cp -f ${CSS_DIR}/gutenberg.css ${GH_PAGES_DIR}/index.css
curl -o ${GH_PAGES_DIR}/screenshot.gif https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-1.gif
