#!/bin/sh
./vendor/bin/phpcs --standard=PSR2 --encoding=utf-8 src/ tests/
./vendor/bin/phpmd src/ text cleancode codesize,controversial,design,naming,unusedcode
./vendor/bin/phpmd tests/ text cleancode codesize,controversial,design,naming,unusedcode