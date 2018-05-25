#!/bin/bash
cd $(dirname $0)/../../
echo "Running in : $(pwd)"
./vendor/bin/phpcbf --standard=tests/phpcs.xml
