#!/bin/bash
cd "$(dirname $0)/../../"
./cc-test-reporter format-coverage --input-type=clover --output build/logs/codeclimate.json
./cc-test-reporter upload-coverage --input build/logs/codeclimate.json
