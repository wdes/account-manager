#!/usr/bin/env bash
ME="$(dirname $0)"
cd "$ME/../../"

CODACY_LATEST_PHAR=$(curl -s https://api.github.com/repos/codacy/php-codacy-coverage/releases/latest | grep browser_download_url | cut -d '"' -f 4)

curl -L "$CODACY_LATEST_PHAR" > ./codacy-coverage.phar
curl -L https://codeclimate.com/downloads/test-reporter/test-reporter-latest-linux-amd64 > ./cc-test-reporter

chmod +x ./codacy-coverage.phar
chmod +x ./cc-test-reporter

if [[ "$TRAVIS_OS_NAME" != "osx" ]]; then ./cc-test-reporter before-build; fi
