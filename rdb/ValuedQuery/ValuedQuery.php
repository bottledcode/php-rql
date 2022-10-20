<?php

namespace r\ValuedQuery;

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

    public function update($delta, $opts = null): Update
    {
        return new Update($this, $delta, $opts);
    }

    public function delete($opts = null): Delete
    {
        return new Delete($this, $opts);
    }

    public function replace($delta, $opts = null): Replace
    {
        return new Replace($this, $delta, $opts);
    }

    public function between($leftBound, $rightBound, $opts = null): Between
    {
        return new Between($this, $leftBound, $rightBound, $opts);
    }

    public function filter($predicate, $default = null): Filter
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

    public function mapMultiple($moreSequences, $mappingFunction): MapMultiple
    {
        return new MapMultiple($this, $moreSequences, $mappingFunction);
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

    public function union(ValuedQuery $otherSequence, $opts = null): Union
    {
        return new Union($this, $otherSequence, $opts);
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

    public function add($other): Add
    {
        return new Add($this, $other);
    }

    public function sub($other): Sub
    {
        return new Sub($this, $other);
    }

    public function mul($other): Mul
    {
        return new Mul($this, $other);
    }

    public function div($other): Div
    {
        return new Div($this, $other);
    }

    public function mod($other): Mod
    {
        return new Mod($this, $other);
    }

    public function rAnd($other): RAnd
    {
        return new RAnd($this, $other);
    }

    public function rOr($other): ROr
    {
        return new ROr($this, $other);
    }

    public function eq($other): Eq
    {
        return new Eq($this, $other);
    }

    public function ne($other): Ne
    {
        return new Ne($this, $other);
    }

    public function gt($other): Gt
    {
        return new Gt($this, $other);
    }

    public function ge($other): Ge
    {
        return new Ge($this, $other);
    }

    public function lt($other): Lt
    {
        return new Lt($this, $other);
    }

    public function le($other): Le
    {
        return new Le($this, $other);
    }

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

    public function ceil(): Ceil
    {
        return new Ceil($this);
    }

    public function floor(): Floor
    {
        return new Floor($this);
    }

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

    public function intersects($g2): Intersects
    {
        return new Intersects($this, $g2);
    }

    public function includes($g2): Includes
    {
        return new Includes($this, $g2);
    }

    public function distance($g2, $opts = null): Distance
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
