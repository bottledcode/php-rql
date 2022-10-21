<?php

namespace r\ValuedQuery;

use r\Options\BetweenOptions;
use r\Options\DeleteOptions;
use r\Options\DistanceOptions;
use r\Options\UnionOptions;
use r\Options\UpdateOptions;
use r\Queries\Aggregations\Avg;
use r\Queries\Aggregations\Contains;
use r\Queries\Aggregations\Count;
use r\Queries\Aggregations\Distinct;
use r\Queries\Aggregations\Fold;
use r\Queries\Aggregations\Group;
use r\Queries\Aggregations\Max;
use r\Queries\Aggregations\Min;
use r\Queries\Aggregations\Reduce;
use r\Queries\Aggregations\Sum;
use r\Queries\Aggregations\Ungroup;
use r\Queries\Control\Changes;
use r\Queries\Control\CoerceTo;
use r\Queries\Control\RDo;
use r\Queries\Control\RForeach;
use r\Queries\Control\ToJsonString;
use r\Queries\Control\TypeOf;
use r\Queries\Dates\Date;
use r\Queries\Dates\Day;
use r\Queries\Dates\DayOfWeek;
use r\Queries\Dates\DayOfYear;
use r\Queries\Dates\During;
use r\Queries\Dates\Hours;
use r\Queries\Dates\InTimezone;
use r\Queries\Dates\Minutes;
use r\Queries\Dates\Month;
use r\Queries\Dates\Seconds;
use r\Queries\Dates\TimeOfDay;
use r\Queries\Dates\Timezone;
use r\Queries\Dates\ToEpochTime;
use r\Queries\Dates\ToIso8601;
use r\Queries\Dates\Year;
use r\Queries\Geo\Distance;
use r\Queries\Geo\Fill;
use r\Queries\Geo\Includes;
use r\Queries\Geo\Intersects;
use r\Queries\Geo\PolygonSub;
use r\Queries\Geo\ToGeoJSON;
use r\Queries\Joins\EqJoin;
use r\Queries\Joins\InnerJoin;
use r\Queries\Joins\OuterJoin;
use r\Queries\Joins\Zip;
use r\Queries\Manipulation\Append;
use r\Queries\Manipulation\Bracket;
use r\Queries\Manipulation\ChangeAt;
use r\Queries\Manipulation\DeleteAt;
use r\Queries\Manipulation\Difference;
use r\Queries\Manipulation\GetField;
use r\Queries\Manipulation\HasFields;
use r\Queries\Manipulation\InsertAt;
use r\Queries\Manipulation\Keys;
use r\Queries\Manipulation\Merge;
use r\Queries\Manipulation\Pluck;
use r\Queries\Manipulation\Prepend;
use r\Queries\Manipulation\SetDifference;
use r\Queries\Manipulation\SetInsert;
use r\Queries\Manipulation\SetIntersection;
use r\Queries\Manipulation\SetUnion;
use r\Queries\Manipulation\SpliceAt;
use r\Queries\Manipulation\Values;
use r\Queries\Manipulation\Without;
use r\Queries\Math\Add;
use r\Queries\Math\Ceil;
use r\Queries\Math\Div;
use r\Queries\Math\Downcase;
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
use r\Queries\Math\ROr;
use r\Queries\Math\Round;
use r\Queries\Math\RqlMatch;
use r\Queries\Math\Split;
use r\Queries\Math\Sub;
use r\Queries\Math\Upcase;
use r\Queries\Selecting\Between;
use r\Queries\Selecting\Filter;
use r\Queries\Transformations\ConcatMap;
use r\Queries\Transformations\IsEmpty;
use r\Queries\Transformations\Limit;
use r\Queries\Transformations\Map;
use r\Queries\Transformations\MapMultiple;
use r\Queries\Transformations\Nth;
use r\Queries\Transformations\OffsetsOf;
use r\Queries\Transformations\OrderBy;
use r\Queries\Transformations\Sample;
use r\Queries\Transformations\Skip;
use r\Queries\Transformations\Slice;
use r\Queries\Transformations\Union;
use r\Queries\Transformations\WithFields;
use r\Queries\Writing\Delete;
use r\Queries\Writing\Replace;
use r\Queries\Writing\Update;
use r\Query;

abstract class ValuedQuery extends Query
{
    public function __invoke($attributeOrIndex): Bracket
    {
        return new Bracket($this, $attributeOrIndex);
    }

    /**
     * Update JSON documents in a table. Accepts a JSON document, a ReQL expression, or a combination of the two.
     * @param array|object|callable $delta
     * @param UpdateOptions $opts
     * @return Update
     */
    public function update(array|object|callable $delta, UpdateOptions $opts = new UpdateOptions()): Update
    {
        return new Update($this, $delta, $opts);
    }

    /**
     * Delete one or more documents from a table.
     * @param DeleteOptions $opts
     * @return Delete
     */
    public function delete(DeleteOptions $opts = new DeleteOptions()): Delete
    {
        return new Delete($this, $opts);
    }

    /**
     * Replace documents in a table. Accepts a JSON document or a ReQL expression, and replaces the original document
     * with the new one. The new document must have the same primary key as the original document.
     *
     * The replace command can be used to both insert and delete documents. If the “replaced” document has a primary
     * key that doesn’t exist in the table, the document will be inserted; if an existing document is replaced with
     * null, the document will be deleted. Since update and replace operations are performed atomically, this allows
     * atomic inserts and deletes as well.
     * @param array|object|callable $delta
     * @param UpdateOptions $opts
     * @return Replace
     */
    public function replace(array|object|callable $delta, UpdateOptions $opts = new UpdateOptions()): Replace
    {
        return new Replace($this, $delta, $opts);
    }

    /**
     * Get all documents between two keys. Accepts three optional arguments: index, leftBound, and rightBound. If
     * index is set to the name of a secondary index, between will return all documents where that index’s value is in
     * the specified range (it uses the primary key by default). leftBound or rightBound may be set to open or closed
     * to indicate whether or not to include that endpoint of the range (by default, leftBound is closed and rightBound
     * is open).
     *
     * You may also use the special constants r.minval and r.maxval for boundaries, which represent “less than any
     * index key” and “more than any index key” respectively. For instance, if you use r.minval as the lower key, then
     * between will return all documents whose primary keys (or indexes) are less than the specified upper key.
     *
     * If you use arrays as indexes (compound indexes), they will be sorted using lexicographical order.
     *
     * The between command works with secondary indexes on date fields, but will not work with unindexed date fields.
     * To test whether a date value is between two other dates, use the during command, not between.
     *
     * Secondary indexes can be used in extremely powerful ways with between and other commands; read the full article
     * on secondary indexes for examples using boolean operations, contains and more.
     *
     * RethinkDB uses byte-wise ordering for between and does not support Unicode collations; non-ASCII characters will
     * be sorted by UTF-8 codepoint.
     * @param mixed $leftBound
     * @param mixed $rightBound
     * @param BetweenOptions $opts
     * @return Between
     */
    public function between(mixed $leftBound, mixed $rightBound, BetweenOptions $opts = new BetweenOptions()): Between
    {
        return new Between($this, $leftBound, $rightBound, $opts);
    }

    /**
     * Return all the elements in a sequence for which the given predicate is true. The return value of filter will be
     * the same as the input (sequence, stream, or array). Documents can be filtered in a variety of ways—ranges,
     * nested values, boolean conditions, and the results of anonymous functions.
     *
     * By default, filter will silently skip documents with missing fields: if the predicate tries to access a field
     * that doesn’t exist (for instance, the predicate {age: 30} applied to a document with no age field), that
     * document will not be returned in the result set, and no error will be generated. This behavior can be changed
     * with the default optional argument.
     * @param callable|Query $predicate
     * @param mixed|null $default
     * @return Filter
     */
    public function filter(callable|Query $predicate, mixed $default = null): Filter
    {
        return new Filter($this, $predicate, $default);
    }

    public function innerJoin(ValuedQuery $otherSequence, $predicate): InnerJoin
    {
        return new InnerJoin($this, $otherSequence, $predicate);
    }

    public function outerJoin(ValuedQuery $otherSequence, $predicate): OuterJoin
    {
        return new OuterJoin($this, $otherSequence, $predicate);
    }

    public function eqJoin($attribute, ValuedQuery $otherSequence, $opts = null): EqJoin
    {
        return new EqJoin($this, $attribute, $otherSequence, $opts);
    }

    public function zip(): Zip
    {
        return new Zip($this);
    }

    public function withFields($attributes): WithFields
    {
        return new WithFields($this, $attributes);
    }

    public function map($mappingFunction): Map
    {
        return new Map($this, $mappingFunction);
    }

    /**
     * Transform each element of one or more sequences by applying a mapping function to them. If map is run with two or
     * more sequences, it will iterate for as many items as there are in the shortest sequence.
     *
     * Note that map can only be applied to sequences, not single values. If you wish to apply a function to a single
     * value/selection (including an array), use the do command.
     *
     * @param array|Query $sequences
     * @param callable|Query|array ...$mappingFunction
     * @return MapMultiple
     */
    public function mapMultiple(callable|Query|array ...$mappingFunction): MapMultiple
    {
        return new MapMultiple($this, ...$mappingFunction);
    }

    public function concatMap($mappingFunction): ConcatMap
    {
        return new ConcatMap($this, $mappingFunction);
    }

    public function orderBy($keys): OrderBy
    {
        return new OrderBy($this, $keys);
    }

    public function skip($n): Skip
    {
        return new Skip($this, $n);
    }

    public function limit($n): Limit
    {
        return new Limit($this, $n);
    }

    public function slice($startIndex, $endIndex = null, $opts = null): Slice
    {
        return new Slice($this, $startIndex, $endIndex, $opts);
    }

    public function nth($index): Nth
    {
        return new Nth($this, $index);
    }

    public function offsetsOf($predicate): OffsetsOf
    {
        return new OffsetsOf($this, $predicate);
    }

    public function isEmpty(): IsEmpty
    {
        return new IsEmpty($this);
    }

    /**
     * Merge two or more sequences.
     * @see https://rethinkdb.com/api/javascript/union
     * @param array|Query|UnionOptions ...$otherSequences
     * @return Union
     */
    public function union(array|Query|UnionOptions ...$otherSequences): Union
    {
        return new Union($this, ...$otherSequences);
    }

    public function sample($n): Sample
    {
        return new Sample($this, $n);
    }

    public function reduce($reductionFunction): Reduce
    {
        return new Reduce($this, $reductionFunction);
    }

    public function fold($base, $fun, $opts = null): Fold
    {
        return new Fold($this, $base, $fun, $opts);
    }

    public function count($filter = null): Count
    {
        return new Count($this, $filter);
    }

    public function distinct($opts = null): Distinct
    {
        return new Distinct($this, $opts);
    }

    public function group($groupOn): Group
    {
        return new Group($this, $groupOn);
    }

    public function ungroup(): Ungroup
    {
        return new Ungroup($this);
    }

    public function avg($attribute = null): Avg
    {
        return new Avg($this, $attribute);
    }

    public function sum($attribute = null): Sum
    {
        return new Sum($this, $attribute);
    }

    public function min($attributeOrOpts = null): Min
    {
        return new Min($this, $attributeOrOpts);
    }

    public function max($attributeOrOpts = null): Max
    {
        return new Max($this, $attributeOrOpts);
    }
    // Note: The API docs suggest that as of 1.6, contains can accept multiple values.
    //  We do not support that for the time being.
    public function contains($value): Contains
    {
        return new Contains($this, $value);
    }

    public function pluck($attributes): Pluck
    {
        return new Pluck($this, $attributes);
    }

    public function without($attributes): Without
    {
        return new Without($this, $attributes);
    }

    public function merge($other): Merge
    {
        return new Merge($this, $other);
    }

    public function append($value): Append
    {
        return new Append($this, $value);
    }

    public function prepend($value): Prepend
    {
        return new Prepend($this, $value);
    }

    public function difference($value): Difference
    {
        return new Difference($this, $value);
    }

    public function setInsert($value): SetInsert
    {
        return new SetInsert($this, $value);
    }

    public function setUnion($value): SetUnion
    {
        return new SetUnion($this, $value);
    }

    public function setIntersection($value): SetIntersection
    {
        return new SetIntersection($this, $value);
    }

    public function setDifference($value): SetDifference
    {
        return new SetDifference($this, $value);
    }

    public function getField($attribute): GetField
    {
        return new GetField($this, $attribute);
    }

    public function hasFields($attributes): HasFields
    {
        return new HasFields($this, $attributes);
    }

    public function insertAt($index, $value): InsertAt
    {
        return new InsertAt($this, $index, $value);
    }

    public function spliceAt($index, $value): SpliceAt
    {
        return new SpliceAt($this, $index, $value);
    }

    public function deleteAt($index, $endIndex = null): DeleteAt
    {
        return new DeleteAt($this, $index, $endIndex);
    }

    public function changeAt($index, $value): ChangeAt
    {
        return new ChangeAt($this, $index, $value);
    }

    public function keys(): Keys
    {
        return new Keys($this);
    }

    public function values(): Values
    {
        return new Values($this);
    }

    /**
     * Sum two or more numbers, or concatenate two or more strings or arrays.
     *
     * The add command can be called in either prefix or infix form; both forms are equivalent. Note that ReQL will not
     * perform type coercion. You cannot, for example, add a string and a number together.
     * @see https://rethinkdb.com/api/javascript/add/
     * @param string|int|float|Query $other
     * @return Add
     */
    public function add(string|int|float|Query $other): Add
    {
        return new Add($this, $other);
    }

    /**
     * Subtract two numbers.
     * @see https://rethinkdb.com/api/javascript/sub/
     * @param int|float|Query|\DateTimeInterface $other
     * @return Sub
     */
    public function sub(int|float|Query|\DateTimeInterface $other): Sub
    {
        return new Sub($this, $other);
    }

    /**
     * Multiply two numbers, or make a periodic array.
     * @see https://rethinkdb.com/api/javascript/mul/
     * @param int|float|Query $other
     * @return Mul
     */
    public function mul(int|float|Query $other): Mul
    {
        return new Mul($this, $other);
    }

    /**
     * Divide two numbers.
     * @see https://rethinkdb.com/api/javascript/div/
     * @param int|float|Query $other
     * @return Div
     */
    public function div(int|float|Query $other): Div
    {
        return new Div($this, $other);
    }

    /**
     * Find the remainder when dividing two numbers.
     * @see https://rethinkdb.com/api/javascript/mod/
     * @param float|int|Query $other
     * @return Mod
     */
    public function mod(float|int|Query $other): Mod
    {
        return new Mod($this, $other);
    }

    /**
     * Compute the logical “and” of one or more values.
     *
     * The and command can be used as an infix operator after its first argument (r.expr(true).and(false)) or given all of
     * its arguments as parameters (r.and(true,false)).
     * @see https://rethinkdb.com/api/javascript/and/
     * @param bool|Query $other
     * @return RAnd
     */
    public function rAnd(bool|Query $other): RAnd
    {
        return new RAnd($this, $other);
    }

    /**
     * Compute the logical “or” of one or more values.
     *
     * The or command can be used as an infix operator after its first argument (r.expr(true).or(false)) or given all of
     * its arguments as parameters (r.or(true,false)).
     * @see https://rethinkdb.com/api/javascript/or/
     * @param bool|Query $other
     * @return ROr
     */
    public function rOr(bool|Query $other): ROr
    {
        return new ROr($this, $other);
    }

    /**
     * Test if two or more values are equal.
     * @see https://rethinkdb.com/api/javascript/eq/
     * @param mixed $other
     * @return Eq
     */
    public function eq(mixed $other): Eq
    {
        return new Eq($this, $other);
    }

    /**
     * Test if two or more values are not equal.
     * @see https://rethinkdb.com/api/javascript/ne/
     * @param $other
     * @return Ne
     */
    public function ne($other): Ne
    {
        return new Ne($this, $other);
    }

    /**
     * Compare values, testing if the left-hand value is greater than the right-hand.
     * @see https://rethinkdb.com/api/javascript/gt/
     * @param $other
     * @return Gt
     */
    public function gt($other): Gt
    {
        return new Gt($this, $other);
    }

    /**
     * Compare values, testing if the left-hand value is greater than or equal to the right-hand.
     * @see https://rethinkdb.com/api/javascript/ge/
     * @param mixed $other
     * @return Ge
     */
    public function ge(mixed $other): Ge
    {
        return new Ge($this, $other);
    }

    /**
     * Compare values, testing if the left-hand value is less than the right-hand.
     * @see https://rethinkdb.com/api/javascript/lt/
     * @param mixed $other
     * @return Lt
     */
    public function lt(mixed $other): Lt
    {
        return new Lt($this, $other);
    }

    /**
     * Compare values, testing if the left-hand value is less than or equal to the right-hand.
     * @see https://rethinkdb.com/api/javascript/le/
     * @param mixed $other
     * @return Le
     */
    public function le(mixed $other): Le
    {
        return new Le($this, $other);
    }

    /**
     * Compute the logical inverse (not) of an expression.
     *
     * not can be called either via method chaining, immediately after an expression that evaluates as a boolean value, or
     * by passing the expression as a parameter to not. All values that are not false or null will be converted to true.
     *
     * @return Not
     */
    public function not(): Not
    {
        return new Not($this);
    }

    public function match($expression): RqlMatch
    {
        return new RqlMatch($this, $expression);
    }

    public function upcase(): Upcase
    {
        return new Upcase($this);
    }

    public function downcase(): Downcase
    {
        return new Downcase($this);
    }

    public function split($separator = null, $maxSplits = null): Split
    {
        return new Split($this, $separator, $maxSplits);
    }

    /**
     * Rounds the given value up, returning the smallest integer value greater than or equal to the given value (the
     * value’s ceiling).
     * @return Ceil
     */
    public function ceil(): Ceil
    {
        return new Ceil($this);
    }

    /**
     * Rounds the given value down, returning the largest integer value less than or equal to the given value (the
     * value’s floor).
     * @return Floor
     */
    public function floor(): Floor
    {
        return new Floor($this);
    }

    /**
     * Rounds the given value to the nearest whole integer.
     * @return Round
     */
    public function round(): Round
    {
        return new Round($this);
    }

    public function rForeach($queryFunction): RForeach
    {
        return new RForeach($this, $queryFunction);
    }

    public function coerceTo($typeName): CoerceTo
    {
        return new CoerceTo($this, $typeName);
    }

    public function typeOf(): TypeOf
    {
        return new TypeOf($this);
    }

    public function rDo($inExpr): RDo
    {
        return new RDo($this, $inExpr);
    }

    public function toEpochTime(): ToEpochTime
    {
        return new ToEpochTime($this);
    }

    public function toIso8601(): ToIso8601
    {
        return new ToIso8601($this);
    }

    public function inTimezone($timezone): InTimezone
    {
        return new InTimezone($this, $timezone);
    }

    public function timezone(): Timezone
    {
        return new Timezone($this);
    }

    public function during($startTime, $endTime, $opts = null): During
    {
        return new During($this, $startTime, $endTime, $opts);
    }

    public function date(): Date
    {
        return new Date($this);
    }

    public function timeOfDay(): TimeOfDay
    {
        return new TimeOfDay($this);
    }

    public function year(): Year
    {
        return new Year($this);
    }

    public function month(): Month
    {
        return new Month($this);
    }

    public function day(): Day
    {
        return new Day($this);
    }

    public function dayOfWeek(): DayOfWeek
    {
        return new DayOfWeek($this);
    }

    public function dayOfYear(): DayOfYear
    {
        return new DayOfYear($this);
    }

    public function hours(): Hours
    {
        return new Hours($this);
    }

    public function minutes(): Minutes
    {
        return new Minutes($this);
    }

    public function seconds(): Seconds
    {
        return new Seconds($this);
    }

    public function changes($opts = null): Changes
    {
        return new Changes($this, $opts);
    }

    public function toGeoJSON(): ToGeoJSON
    {
        return new ToGeoJSON($this);
    }

    /**
     * Tests whether two geometry objects intersect with one another. When applied to a sequence of geometry objects,
     * intersects acts as a filter, returning a sequence of objects from the sequence that intersect with the argument.
     * @param Query $g2
     * @return Intersects
     */
    public function intersects(Query $g2): Intersects
    {
        return new Intersects($this, $g2);
    }

    public function includes($g2): Includes
    {
        return new Includes($this, $g2);
    }

    /**
     * Compute the distance between a point and another geometry object. At least one of the geometry objects specified
     * must be a point.
     *
     * If one of the objects is a polygon or a line, the point will be projected onto the line or polygon assuming a
     * perfect sphere model before the distance is computed (using the model specified with geoSystem). As a consequence,
     * if the polygon or line is extremely large compared to Earth’s radius and the distance is being computed with the
     * default WGS84 model, the results of distance should be considered approximate due to the deviation between the
     * ellipsoid and spherical models.
     * @param Query $g2
     * @param DistanceOptions $opts
     * @return Distance
     */
    public function distance(Query $g2, DistanceOptions $opts = new DistanceOptions()): Distance
    {
        return new Distance($this, $g2, $opts);
    }

    public function fill(): Fill
    {
        return new Fill($this);
    }

    public function polygonSub($other): PolygonSub
    {
        return new PolygonSub($this, $other);
    }

    public function toJsonString(): ToJsonString
    {
        return new ToJsonString($this);
    }
}
