#!/bin/bash -ex

# update dependencies
PWD=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
BASEDIR=`dirname $PWD`

# db
# try wercker ports
RDB_HOST="${RDB_HOST=$RETHINKDB_PORT_28015_TCP_ADDR}"
RDB_PORT="${RDB_PORT=$RETHINKDB_PORT_28015_TCP_PORT}"

# otherwise, use defaults
RDB_HOST="${RDB_HOST:=127.0.0.1}"
RDB_PORT="${RDB_PORT:=28015}"

RDB_DB="${RDB_DB=RQL_TEST_`date +%s`}"
export RDB_HOST
export RDB_PORT
export RDB_DB

composer require amphp/socket

# run tests
php $BASEDIR/tests/TestHelpers/createDb.php
ASYNC=yes /usr/bin/time -v $BASEDIR/vendor/bin/phpunit -c $PWD/phpunit.xml --colors=always "$@"
php $BASEDIR/tests/TestHelpers/deleteDb.php
php $BASEDIR/tests/TestHelpers/createDb.php
ASYNC=no /usr/bin/time -v $BASEDIR/vendor/bin/phpunit -c $PWD/phpunit.xml --colors=always "$@"
STATUS=$?

#remove db
php $BASEDIR/tests/TestHelpers/deleteDb.php

# exit
exit $STATUS
