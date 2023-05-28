<?php

use r\Options\Durability;
use r\Options\TableCreateOptions;

include __DIR__ . '../../../vendor/autoload.php';

$conn = \r\connect(new \r\ConnectionOptions(host: getenv('RDB_HOST') ?: 'localhost', port: getenv('RDB_PORT') ?: 28015));
$db = getenv('RDB_DB') ?: ('test-'. random_int(0, 100000));
$res = r\dbCreate($db)->run($conn);

if ($res['dbs_created'] !== 1) {
    echo 'Error creating DB' . PHP_EOL;
    exit;
}

r\db($db)->tableCreate('marvel', new TableCreateOptions(primaryKey: 'superhero'))->run($conn);
r\db($db)->tableCreate('dc_universe', new TableCreateOptions(primaryKey: 'name'))->run($conn);
r\db($db)->tableCreate('t5000', new TableCreateOptions(durability: Durability::Soft))->run($conn);

$tables = array(
    't1',
    't2',
    'geo'
);

foreach ($tables as $table) {
    r\db($db)->tableCreate($table)->run($conn);
}

r\db($db)->table('t1')->indexCreate('other')->run($conn);
r\db($db)->table('t2')->indexCreate('other')->run($conn);

$geoTable = r\db($db)->table('geo');
$geoTable->indexCreateGeo('geo')->run($conn);
$geoTable->indexCreateMultiGeo('mgeo', function ($x) {
    return r\expr(array($x('geo')));
})->run($conn);
$geoTable->indexWait('geo')->run($conn);
$geoTable->indexWait('mgeo')->run($conn);
