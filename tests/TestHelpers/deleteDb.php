<?php

include __DIR__ . '../../../vendor/autoload.php';

$conn = \r\connect(new \r\ConnectionOptions(host: getenv('RDB_HOST'), port: getenv('RDB_PORT')));

$res = r\dbDrop(getenv('RDB_DB'))->run($conn);
