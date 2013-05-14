#!/bin/bash

PHP=`which php`
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
$PHP $DIR/modx-cli-tool.php $@
