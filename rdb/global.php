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
use r\Options\BinaryFormat;
use r\Options\HttpOptions;
use r\Options\TableCreateOptions;
use r\Options\TableOptions;
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

/**
 * Connect to a database.
 *
 * @see https://rethinkdb.com/api/javascript/connect/
 * @param ConnectionOptions $connectionOptions
 * @return Connection
 */
function connect(
    ConnectionOptions $connectionOptions
): Connection {
    return new Connection($connectionOptions);
}

/**
 * Reference a database.
 *
 * The db command is optional. If it is not present in a query, the query will run against the default database for the
 * connection, specified in the db argument to connect.
 * @see https://rethinkdb.com/api/javascript/db/
 * @param string $dbName The name of the database to use.
 * @return Db
 */
function db(string $dbName): Db
{
    return new Db($dbName);
}

/**
 * Create a database. A RethinkDB database is a collection of tables, similar to relational databases.
 *
 * If successful, the command returns an object with two fields:
 *
 * = dbs_created: always 1.
 * = config_changes: a list containing one object with two fields, old_val and new_val:
 *  = old_val: always null.
 *  = new_val: the database’s new config value.
 * If a database with the same name already exists, the command throws ReqlRuntimeError.
 *
 * Note: Only alphanumeric characters, hyphens and underscores are valid for the database name.
 * @see https://rethinkdb.com/api/javascript/db_create
 * @param string $dbName The name of the database to create.
 * @return DbCreate
 */
function dbCreate(string $dbName): DbCreate
{
    return new DbCreate($dbName);
}

/**
 * Drop a database. The database, all its tables, and corresponding data will be deleted.
 *
 * If successful, the command returns an object with two fields:
 *
 * - dbs_dropped: always 1.
 * - tables_dropped: the number of tables in the dropped database.
 * - config_changes: a list containing one two-field object, old_val and new_val:
 *  - old_val: the database’s original config value.
 *  - new_val: always null.
 * If the given database does not exist, the command throws ReqlRuntimeError.
 * @see https://rethinkdb.com/api/javascript/db_drop
 * @param string $dbName The database to drop
 * @return DbDrop
 */
function dbDrop(string $dbName): DbDrop
{
    return new DbDrop($dbName);
}

/**
 * List all database names in the system. The result is a list of strings.
 * @see https://rethinkdb.com/api/javascript/db_list
 * @return DbList
 */
function dbList(): DbList
{
    return new DbList();
}

/**
 * Return all documents in a table. Other commands may be chained after table to return a subset of documents (such as
 * get and filter) or perform further processing.
 * @see https://rethinkdb.com/api/javascript/table/
 * @param string $tableName The name of the table to read from.
 * @param TableOptions $options
 * @return Table
 * @throws RqlDriverError
 */
function table(string $tableName, TableOptions $options = new TableOptions()): Table
{
    return new Table(null, $tableName, $options);
}

/**
 * Create a table. A RethinkDB table is a collection of JSON documents.
 *
 * If successful, the command returns an object with two fields:
 *
 * - tables_created: always 1.
 * - config_changes: a list containing one two-field object, old_val and new_val:
 *   - old_val: always null.
 *   - new_val: the table’s new config value.
 * If a table with the same name already exists, the command throws ReqlOpFailedError.
 * @see https://rethinkdb.com/api/javascript/table_create/
 * @param string $tableName The table name to create
 * @param TableCreateOptions $options
 * @return TableCreate
 */
function tableCreate(string $tableName, TableCreateOptions $options = new TableCreateOptions()): TableCreate
{
    return new TableCreate(null, $tableName, $options);
}

/**
 * Drop a table from a database. The table and all its data will be deleted.
 *
 * If successful, the command returns an object with two fields:
 *
 * - tables_dropped: always 1.
 * - config_changes: a list containing one two-field object, old_val and new_val:
 *   - old_val: the dropped table’s config value.
 *   - new_val: always null.
 * If the given table does not exist in the database, the command throws ReqlRuntimeError.
 * @see https://rethinkdb.com/api/javascript/table_drop
 * @param string $tableName The table to drop
 * @return TableDrop
 */
function tableDrop(string $tableName): TableDrop
{
    return new TableDrop(null, $tableName);
}

/**
 * List all table names in a database. The result is a list of strings.
 * @see https://rethinkdb.com/api/javascript/table_list
 * @return TableList
 */
function tableList(): TableList
{
    return new TableList(null);
}

/**
 * Call an anonymous function using return values from other ReQL commands or queries as arguments.
 *
 * The last argument to do (or, in some forms, the only argument) is an expression or an anonymous function which
 * receives values from either the previous arguments or from prefixed commands chained before do. The do command is
 * essentially a single-element map, letting you map a function over just one document. This allows you to bind a query
 * result to a local variable within the scope of do, letting you compute the result just once and reuse it in a complex
 * expression or in a series of ReQL commands.
 *
 * Arguments passed to the do function must be basic data types, and cannot be streams or selections. (Read about ReQL
 * data types.) While the arguments will all be evaluated before the function is executed, they may be evaluated in any
 * order, so their values should not be dependent on one another. The type of do’s result is the type of the value
 * returned from the function or last expression.
 * @see https://rethinkdb.com/api/javascript/do/
 * @param array $args
 * @param Query|callable $inExpr
 * @return RDo
 */
function rDo(array $args, Query|callable $inExpr): RDo
{
    return new RDo($args, $inExpr);
}

/**
 * r.args is a special term that’s used to splice an array of arguments into another term. This is useful when you want
 * to call a variadic term such as getAll with a set of arguments produced at runtime.
 *
 * This is analogous to using apply in JavaScript. (However, note that args evaluates all its arguments before passing
 * them into the parent term, even if the parent term otherwise allows lazy evaluation.)
 * @see https://rethinkdb.com/api/javascript/args
 * @param array $args
 * @return Args
 */
function args(array $args): Args
{
    return new Args($args);
}

/**
 * Perform a branching conditional equivalent to if-then-else.
 *
 * The branch command takes 2n+1 arguments: pairs of conditional expressions and commands to be executed if the
 * conditionals return any value but false or null (i.e., “truthy” values), with a final “else” command to be evaluated
 * if all of the conditionals are false or null.
 * @see https://rethinkdb.com/api/javascript/branch
 * @param Query $test
 * @param ...$branches
 * @return Branch
 */
function branch(Query $test, ...$branches): Branch
{
    return new Branch($test, ...$branches);
}

/**
 * Returns the currently visited document.
 * @see https://rethinkdb.com/api/javascript/row/
 * @param string|Query|null $attribute Shortcut for row()(attribute)
 * @return GetField|ImplicitVar
 */
function row(string|Query|null $attribute = null): GetField|ImplicitVar
{
    if (null !== $attribute) {
        // A shortcut to do row()($attribute)
        return new GetField(new ImplicitVar(), $attribute);
    } else {
        return new ImplicitVar();
    }
}

/**
 * Create a javascript expression.
 *
 * timeout is the number of seconds before r.js times out. The default value is 5 seconds.
 *
 * Whenever possible, you should use native ReQL commands rather than r.js for better performance.
 * @see https://rethinkdb.com/api/javascript/js/
 * @param string $code The js code to execute
 * @param int|float|null $timeout The timeout in seconds
 * @return Js
 */
function js(string $code, int|null|float $timeout = null): Js
{
    return new Js($code, $timeout);
}

/**
 * Throw a runtime error. If called with no arguments inside the second argument to default, re-throw the current error.
 * @see https://rethinkdb.com/api/javascript/error/
 * @param string|null $message
 * @return Error
 */
function error(string|null $message = null): Error
{
    return new Error($message);
}

/**
 * Construct a ReQL JSON object from a native object.
 *
 * @param mixed $obj
 * @return MakeObject|ObjectDatum|Iso8601|MakeArray|StringDatum|BoolDatum|NumberDatum|Query|NullDatum|ArrayDatum
 * @throws RqlDriverError
 */
function expr(
    mixed $obj
): MakeObject|ObjectDatum|Iso8601|MakeArray|StringDatum|BoolDatum|NumberDatum|Query|NullDatum|ArrayDatum {
    if ($obj instanceof Query) {
        return $obj;
    }

    $dc = new DatumConverter;
    return $dc->nativeToDatum($obj);
}

/**
 * Encapsulate binary data within a query.
 * @see https://rethinkdb.com/api/javascript/binary/
 * @param string $str
 * @return Datum
 * @throws RqlDriverError
 */
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

/**
 * @see https://rethinkdb.com/api/javascript/order_by/
 *
 * @param callable|string $attribute
 * @return Desc
 */
function desc(callable|string $attribute): Desc
{
    return new Desc($attribute);
}

/**
 * @see https://rethinkdb.com/api/javascript/order_by/
 * @param callable|string $attribute
 * @return Asc
 */
function asc(callable|string $attribute): Asc
{
    return new Asc($attribute);
}

/**
 * Parse a JSON string on the server.
 * @see https://rethinkdb.com/api/javascript/json/
 * @param string|Query $json
 * @return Json
 */
function json(string|Query $json): Json
{
    return new Json($json);
}

/**
 * Retrieve data from the specified URL over HTTP. The return type depends on the resultFormat option, which checks the
 * Content-Type of the response by default. Make sure that you never use this command for user provided URLs.
 * @see https://rethinkdb.com/api/javascript/http/
 * @param string $url
 * @param HttpOptions $opts
 * @return Http
 */
function http(string $url, HttpOptions $opts = new HttpOptions()): Http
{
    return new Http($url, $opts);
}

/**
 * Creates an object from a list of key-value pairs, where the keys must be strings. r.object(A, B, C, D) is equivalent
 * to r.expr([[A, B], [C, D]]).coerceTo('OBJECT').
 * @see https://rethinkdb.com/api/javascript/object/
 * @param mixed ...$object
 * @return RObject
 */
function rObject(mixed ...$object): RObject
{
    return new RObject(...$object);
}

/**
 * Replace an object in a field instead of merging it with an existing object in a merge or update operation. Using
 * literal with no arguments in a merge or update operation will remove the corresponding field.
 * @see https://rethinkdb.com/api/javascript/literal/
 * @param ...$args
 * @return Literal
 */
function literal(...$args): Literal
{
    if (count($args) == 0) {
        return new Literal();
    } else {
        return new Literal($args[0]);
    }
}

/**
 * Sum two or more numbers, or concatenate two or more strings or arrays.
 *
 * The add command can be called in either prefix or infix form; both forms are equivalent. Note that ReQL will not
 * perform type coercion. You cannot, for example, add a string and a number together.
 * @see https://rethinkdb.com/api/javascript/add/
 * @param string|int|float|array|Query $expr1
 * @param string|int|float|array|Query $expr2
 * @return Add
 */
function add(string|int|float|array|Query $expr1, string|int|float|array|Query $expr2): Add
{
    return new Add($expr1, $expr2);
}

/**
 * Subtract two numbers.
 * @see https://rethinkdb.com/api/javascript/sub/
 * @param int|float|Query $expr1
 * @param int|float|Query $expr2
 * @return Sub
 */
function sub(int|float|Query $expr1, int|float|Query $expr2): Sub
{
    return new Sub($expr1, $expr2);
}

/**
 * Multiply two numbers, or make a periodic array.
 * @see https://rethinkdb.com/api/javascript/mul/
 * @param int|float|Query $expr1
 * @param int|float|Query $expr2
 * @return Mul
 */
function mul(int|float|Query $expr1, int|float|Query $expr2): Mul
{
    return new Mul($expr1, $expr2);
}

/**
 * Divide two numbers.
 * @see https://rethinkdb.com/api/javascript/div/
 * @param int|float|Query $expr1
 * @param int|float|Query $expr2
 * @return Div
 */
function div(int|float|Query $expr1, int|float|Query $expr2): Div
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
