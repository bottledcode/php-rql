<?php

namespace r;

use r\Datum\ArrayDatum;
use r\Datum\BoolDatum;
use r\Datum\Datum;
use r\Datum\NullDatum;
use r\Datum\NumberDatum;
use r\Datum\ObjectDatum;
use r\Datum\StringDatum;
use r\Exceptions\RqlDriverError;
use r\Ordering\Asc;
use r\Ordering\Desc;
use r\Queries\Control\Args;
use r\Queries\Control\Branch;
use r\Queries\Control\Error;
use r\Queries\Control\Http;
use r\Queries\Control\Js;
use r\Queries\Control\Range;
use r\Queries\Control\RDo;
use r\Queries\Dates\April;
use r\Queries\Dates\August;
use r\Queries\Dates\December;
use r\Queries\Dates\EpochTime;
use r\Queries\Dates\February;
use r\Queries\Dates\Friday;
use r\Queries\Dates\Iso8601;
use r\Queries\Dates\January;
use r\Queries\Dates\July;
use r\Queries\Dates\June;
use r\Queries\Dates\March;
use r\Queries\Dates\May;
use r\Queries\Dates\Monday;
use r\Queries\Dates\November;
use r\Queries\Dates\Now;
use r\Queries\Dates\October;
use r\Queries\Dates\Saturday;
use r\Queries\Dates\September;
use r\Queries\Dates\Sunday;
use r\Queries\Dates\Thursday;
use r\Queries\Dates\Time;
use r\Queries\Dates\Tuesday;
use r\Queries\Dates\Wednesday;
use r\Queries\Dbs\Db;
use r\Queries\Dbs\DbCreate;
use r\Queries\Dbs\DbDrop;
use r\Queries\Dbs\DbList;
use r\Queries\Geo\Circle;
use r\Queries\Geo\Distance;
use r\Queries\Geo\GeoJSON;
use r\Queries\Geo\Intersects;
use r\Queries\Geo\Line;
use r\Queries\Geo\Point;
use r\Queries\Geo\Polygon;
use r\Queries\Manipulation\GetField;
use r\Queries\Math\Add;
use r\Queries\Math\Ceil;
use r\Queries\Math\Div;
use r\Queries\Math\Eq;
use r\Queries\Math\Floor;
use r\Queries\Math\Ge;
use r\Queries\Math\Gt;
use r\Queries\Math\Le;
use r\Queries\Math\Lt;
use r\Queries\Math\Mod;
use r\Queries\Math\Mul;
use r\Queries\Math\Ne;
use r\Queries\Math\Not;
use r\Queries\Math\RAnd;
use r\Queries\Math\Random;
use r\Queries\Math\ROr;
use r\Queries\Math\Round;
use r\Queries\Math\Sub;
use r\Queries\Misc\Maxval;
use r\Queries\Misc\Minval;
use r\Queries\Misc\Uuid;
use r\Queries\Tables\Table;
use r\Queries\Tables\TableCreate;
use r\Queries\Tables\TableDrop;
use r\Queries\Tables\TableList;
use r\Queries\Transformations\MapMultiple;
use r\Queries\Transformations\Union;
use r\ValuedQuery\ImplicitVar;
use r\ValuedQuery\Json;
use r\ValuedQuery\Literal;
use r\ValuedQuery\MakeArray;
use r\ValuedQuery\MakeObject;
use r\ValuedQuery\RObject;

// ------------- Global functions in namespace r -------------

function connect(
    array|string|null $optsOrHost = null,
    int|null $port = null,
    string|null $db = null,
    string|null $apiKey = null,
    int|float|null $timeout = null
): Connection {
    return new Connection($optsOrHost, $port, $db, $apiKey, $timeout);
}

function db(string $dbName): Db
{
    return new Db($dbName);
}

function dbCreate(string $dbName): DbCreate
{
    return new DbCreate($dbName);
}

function dbDrop(string $dbName): DbDrop
{
    return new DbDrop($dbName);
}

function dbList(): DbList
{
    return new DbList();
}

function table(string $tableName, bool|array $useOutdatedOrOpts = null): Table
{
    return new Table(null, $tableName, $useOutdatedOrOpts);
}

function tableCreate(string $tableName, array $options = null): TableCreate
{
    return new TableCreate(null, $tableName, $options);
}

function tableDrop(string $tableName): TableDrop
{
    return new TableDrop(null, $tableName);
}

function tableList(): TableList
{
    return new TableList(null);
}

function rDo($args, $inExpr): RDo
{
    return new RDo($args, $inExpr);
}

function args(array $args): Args
{
    return new Args($args);
}

function branch(Query $test, $trueBranch, $falseBranch): Branch
{
    return new Branch($test, $trueBranch, $falseBranch);
}

function row($attribute = null): GetField|ImplicitVar
{
    if (isset($attribute)) {
        // A shortcut to do row()($attribute)
        return new GetField(new ImplicitVar(), $attribute);
    } else {
        return new ImplicitVar();
    }
}

function js(string $code, int|null|float $timeout = null): Js
{
    return new Js($code, $timeout);
}

function error(string $message = null): Error
{
    return new Error($message);
}

function expr(mixed $obj
): MakeObject|ObjectDatum|Iso8601|MakeArray|StringDatum|BoolDatum|NumberDatum|Query|NullDatum|ArrayDatum {
    if ((is_object($obj) && is_subclass_of($obj, Query::class))) {
        return $obj;
    }

    $dc = new DatumConverter;
    return $dc->nativeToDatum($obj);
}

function binary(string $str): Datum
{
    $encodedStr = base64_encode($str);
    if ($encodedStr === false) {
        throw new RqlDriverError("Failed to Base64 encode '" . $str . "'");
    }
    $pseudo = array('$reql_type$' => 'BINARY', 'data' => $encodedStr);

    $dc = new DatumConverter;
    return $dc->nativeToDatum($pseudo);
}

function desc(callable|string $attribute): Desc
{
    return new Desc($attribute);
}

function asc(callable|string $attribute): Asc
{
    return new Asc($attribute);
}

function json(array $json): Json
{
    return new Json($json);
}

function http(string $url, $opts = null): Http
{
    return new Http($url, $opts);
}

function rObject($object): RObject
{
    return new RObject($object);
}

// r\literal can accept 0 or 1 arguments
function literal(...$args): Literal
{
    if (count($args) == 0) {
        return new Literal();
    } else {
        return new Literal($args[0]);
    }
}

function add($expr1, $expr2): Add
{
    return new Add($expr1, $expr2);
}

function sub($expr1, $expr2): Sub
{
    return new Sub($expr1, $expr2);
}

function mul($expr1, $expr2): Mul
{
    return new Mul($expr1, $expr2);
}

function div($expr1, $expr2): Div
{
    return new Div($expr1, $expr2);
}

function mod($expr1, $expr2): Mod
{
    return new Mod($expr1, $expr2);
}

function rAnd($expr1, $expr2): RAnd
{
    return new RAnd($expr1, $expr2);
}

function rOr($expr1, $expr2): ROr
{
    return new ROr($expr1, $expr2);
}

function eq($expr1, $expr2): Eq
{
    return new Eq($expr1, $expr2);
}

function ne($expr1, $expr2): Ne
{
    return new Ne($expr1, $expr2);
}

function gt($expr1, $expr2): Gt
{
    return new Gt($expr1, $expr2);
}

function ge($expr1, $expr2): Ge
{
    return new Ge($expr1, $expr2);
}

function lt($expr1, $expr2): Lt
{
    return new Lt($expr1, $expr2);
}

function le($expr1, $expr2): Le
{
    return new Le($expr1, $expr2);
}

function not($expr): Not
{
    return new Not($expr);
}

function random($left = null, $right = null, $opts = null): Random
{
    return new Random($left, $right, $opts);
}

function now(): Now
{
    return new Now();
}

function time($year, $month, $day, $hourOrTimezone = null, $minute = null, $second = null, $timezone = null): Time
{
    return new Time($year, $month, $day, $hourOrTimezone, $minute, $second, $timezone);
}

function epochTime($epochTime): EpochTime
{
    return new EpochTime($epochTime);
}

function iso8601($iso8601Date, $opts = null): Iso8601
{
    return new Iso8601($iso8601Date, $opts);
}

function monday(): Monday
{
    return new Monday();
}

function tuesday(): Tuesday
{
    return new Tuesday();
}

function wednesday(): Wednesday
{
    return new Wednesday();
}

function thursday(): Thursday
{
    return new Thursday();
}

function friday(): Friday
{
    return new Friday();
}

function saturday(): Saturday
{
    return new Saturday();
}

function sunday(): Sunday
{
    return new Sunday();
}

function january(): January
{
    return new January();
}

function february(): February
{
    return new February();
}

function march(): March
{
    return new March();
}

function april(): April
{
    return new April();
}

function may(): May
{
    return new May();
}

function june(): June
{
    return new June();
}

function july(): July
{
    return new July();
}

function august(): August
{
    return new August();
}

function september(): September
{
    return new September();
}

function october(): October
{
    return new October();
}

function november(): November
{
    return new November();
}

function december(): December
{
    return new December();
}

function geoJSON($geojson): GeoJSON
{
    return new GeoJSON($geojson);
}

function point($lat, $lon): Point
{
    return new Point($lat, $lon);
}

function line($points): Line
{
    return new Line($points);
}

function polygon($points): Polygon
{
    return new Polygon($points);
}

function circle($center, $radius, $opts = null): Circle
{
    return new Circle($center, $radius, $opts);
}

function intersects($g1, $g2): Intersects
{
    return new Intersects($g1, $g2);
}

function distance($g1, $g2, $opts = null): Distance
{
    return new Distance($g1, $g2, $opts);
}

function uuid($str = null): Uuid
{
    return new Uuid($str);
}

function minval(): Minval
{
    return new Minval();
}

function maxval(): Maxval
{
    return new Maxval();
}

function range($startOrEndValue = null, $endValue = null): Range
{
    return new Range($startOrEndValue, $endValue);
}

function mapMultiple(array|object $sequences, $mappingFunction): MapMultiple
{
    if (!is_array($sequences)) {
        $sequences = array($sequences);
    }
    if (sizeof($sequences) < 1) {
        throw new RqlDriverError("At least one sequence must be passed into r\mapMultiple.");
    }
    return new MapMultiple($sequences[0], array_slice($sequences, 1), $mappingFunction);
}

function union($sequence, $otherSequence, $opts = null): Union
{
    return new Union($sequence, $otherSequence, $opts);
}

function ceil($value): Ceil
{
    return new Ceil($value);
}

function floor($value): Floor
{
    return new Floor($value);
}

function round($value): Round
{
    return new Round($value);
}

function systemInfo(): string
{
    return "PHP-RQL Version: " . PHP_RQL_VERSION . "\n";
}
