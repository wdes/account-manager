#!/bin/bash
cd $(dirname $0)/../../
echo "Running in : $(pwd)"
./vendor/bin/phpstan analyse src tests --configuration=phpstan.neon --level=max --memory-limit=512M
