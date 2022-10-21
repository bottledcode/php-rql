<?php

namespace r\ValuedQuery;

use r\Options\BetweenOptions;
use r\Options\ChangesOptions;
use r\Options\DeleteOptions;
use r\Options\DistanceOptions;
use r\Options\EqJoinOptions;
use r\Options\FoldOptions;
use r\Options\SliceOptions;
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

    /**
     * Returns an inner join of two sequences.
     *
     * The returned sequence represents an intersection of the left-hand sequence and the right-hand sequence: each row
     * of the left-hand sequence will be compared with each row of the right-hand sequence to find all pairs of rows
     * which satisfy the predicate. Each matched pair of rows of both sequences are combined into a result row. In most
     * cases, you will want to follow the join with zip to combine the left and right results.
     *
     * Note that innerJoin is slower and much less efficient than using eqJoin or concatMap with getAll. You should
     * avoid using innerJoin in commands when possible.
     * @param ValuedQuery $otherSequence
     * @param callable|Query $predicate
     * @return InnerJoin
     */
    public function innerJoin(ValuedQuery $otherSequence, callable|Query $predicate): InnerJoin
    {
        return new InnerJoin($this, $otherSequence, $predicate);
    }

    /**
     * Returns a left outer join of two sequences. The returned sequence represents a union of the left-hand sequence
     * and the right-hand sequence: all documents in the left-hand sequence will be returned, each matched with a
     * document in the right-hand sequence if one satisfies the predicate condition. In most cases, you will want to
     * follow the join with zip to combine the left and right results.
     *
     * Note that outerJoin is slower and much less efficient than using concatMap with getAll. You should avoid using
     * outerJoin in commands when possible.
     * @param ValuedQuery $otherSequence
     * @param callable|Query $predicate
     * @return OuterJoin
     */
    public function outerJoin(ValuedQuery $otherSequence, callable|Query $predicate): OuterJoin
    {
        return new OuterJoin($this, $otherSequence, $predicate);
    }

    /**
     * Join tables using a field or function on the left-hand sequence matching primary keys or secondary indexes on
     * the right-hand table. eqJoin is more efficient than other ReQL join types, and operates much faster. Documents
     * in the result set consist of pairs of left-hand and right-hand documents, matched when the field on the
     * left-hand side exists and is non-null and an entry with that field’s value exists in the specified index on the
     * right-hand side.
     *
     * The result set of eqJoin is a stream or array of objects. Each object in the returned set will be an object of
     * the form { left: <left-document>, right: <right-document> }, where the values of left and right will be the
     * joined documents. Use the zip command to merge the left and right fields together.
     *
     * The results from eqJoin are, by default, not ordered. The optional ordered: true parameter will cause eqJoin to
     * order the output based on the left side input stream. (If there are multiple matches on the right side for a
     * document on the left side, their order is not guaranteed even if ordered is true.) Requiring ordered results
     * can significantly slow down eqJoin, and in many circumstances this ordering will not be required. (See the first
     * example, in which ordered results are obtained by using orderBy after eqJoin.)
     * @param string|callable|Query $leftFieldOrFunction
     * @param ValuedQuery $otherSequence
     * @param EqJoinOptions $opts
     * @return EqJoin
     */
    public function eqJoin(
        string|callable|Query $leftFieldOrFunction,
        ValuedQuery $otherSequence,
        EqJoinOptions $opts = new EqJoinOptions()
    ): EqJoin {
        return new EqJoin($this, $leftFieldOrFunction, $otherSequence, $opts);
    }

    /**
     * Used to ‘zip’ up the result of a join by merging the ‘right’ fields into ‘left’ fields of each member of the
     * sequence.
     * @return Zip
     */
    public function zip(): Zip
    {
        return new Zip($this);
    }

    /**
     * Plucks one or more attributes from a sequence of objects, filtering out any objects in the sequence that do not
     * have the specified fields. Functionally, this is identical to hasFields followed by pluck on a sequence.
     * @param array|string|object ...$fields
     * @return WithFields
     */
    public function withFields(array|string|object ...$fields): WithFields
    {
        return new WithFields($this, ...$fields);
    }

    /**
     * @param $mappingFunction
     * @return Map
     * @deprecated Use mapMultiple instead
     */
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

    /**
     * Concatenate one or more elements into a single sequence using a mapping function.
     *
     * concatMap works in a similar fashion to map, applying the given function to each element in a sequence, but it
     * will always return a single sequence. If the mapping function returns a sequence, map would produce a sequence
     * of sequences:
     * @param callable|Query $mappingFunction
     * @return ConcatMap
     */
    public function concatMap(callable|Query $mappingFunction): ConcatMap
    {
        return new ConcatMap($this, $mappingFunction);
    }

    /**
     * Sort the sequence by document values of the given key(s). To specify the ordering, wrap the attribute with
     * either r.asc or r.desc (defaults to ascending).
     *
     * Note: RethinkDB uses byte-wise ordering for orderBy and does not support Unicode collations; non-ASCII
     * characters will be sorted by UTF-8 codepoint. For more information on RethinkDB’s sorting order, read the
     * section in ReQL data types.
     *
     * Sorting without an index requires the server to hold the sequence in memory, and is limited to 100,000
     * documents (or the setting of the arrayLimit option for run). Sorting with an index can be done on arbitrarily
     * large tables, or after a between command using the same index. This applies to both secondary indexes and the
     * primary key (e.g., {index: 'id'}).
     *
     * Sorting functions passed to orderBy must be deterministic. You cannot, for instance, order rows using the
     * random command. Using a non-deterministic function with orderBy will raise a ReqlQueryLogicError.
     * @param string|callable|object|array ...$keys
     * @return OrderBy
     */
    public function orderBy(string|callable|object|array ...$keys): OrderBy
    {
        return new OrderBy($this, ...$keys);
    }

    /**
     * Skip a number of elements from the head of the sequence.
     * @param int|Query $n
     * @return Skip
     */
    public function skip(int|Query $n): Skip
    {
        return new Skip($this, $n);
    }

    /**
     * Limit the number of elements in the sequence.
     * @param int|Query $n
     * @return Limit
     */
    public function limit(int|Query $n): Limit
    {
        return new Limit($this, $n);
    }

    /**
     * Return the elements of a sequence within the specified range.
     *
     * slice returns the range between startOffset and endOffset. If only startOffset is specified, slice returns the
     * range from that index to the end of the sequence. Specify leftBound or rightBound as open or closed to indicate
     * whether to include that endpoint of the range by default: closed returns that endpoint, while open does not. By
     * default, leftBound is closed and rightBound is open, so the range (10,13) will return the tenth, eleventh and
     * twelfth elements in the sequence.
     *
     * If endOffset is past the end of the sequence, all elements from startOffset to the end of the sequence will be
     * returned. If startOffset is past the end of the sequence or endOffset is less than startOffset, a zero-element
     * sequence will be returned.
     *
     * Negative startOffset and endOffset values are allowed with arrays; in that case, the returned range counts back
     * from the array’s end. That is, the range (-2) returns the last two elements, and the range of (2,-1) returns the
     * second element through the next-to-last element of the range. An error will be raised on a negative startOffset
     * or endOffset with non-arrays. (An endOffset of −1 is allowed with a stream if rightBound is closed; this behaves
     * as if no endOffset was specified.)
     *
     * If slice is used with a binary object, the indexes refer to byte positions within the object. That is, the range
     * (10,20) will refer to the 10th byte through the 19th byte.
     *
     * With a string, slice behaves similarly, with the indexes referring to Unicode codepoints. String indexes start
     * at 0. (Note that combining codepoints are counted separately.)
     * @param int|Query $startIndex
     * @param int|Query|null $endIndex
     * @param SliceOptions $opts
     * @return Slice
     */
    public function slice(
        int|Query $startIndex,
        int|Query|null $endIndex = null,
        SliceOptions $opts = new SliceOptions()
    ): Slice {
        return new Slice($this, $startIndex, $endIndex, $opts);
    }

    /**
     * Get the nth element of a sequence, counting from zero. If the argument is negative, count from the last element.
     * @param int|Query $index
     * @return Nth
     */
    public function nth(int|Query $index): Nth
    {
        return new Nth($this, $index);
    }

    /**
     * Get the indexes of an element in a sequence. If the argument is a predicate, get the indexes of all elements
     * matching it.
     * @param mixed $predicate
     * @return OffsetsOf
     */
    public function offsetsOf(mixed $predicate): OffsetsOf
    {
        return new OffsetsOf($this, $predicate);
    }

    /**
     * Test if a sequence is empty.
     * @return IsEmpty
     */
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

    /**
     * Select a given number of elements from a sequence with uniform random distribution. Selection is done without
     * replacement.
     *
     * If the sequence has less than the requested number of elements (i.e., calling sample(10) on a sequence with only
     * five elements), sample will return the entire sequence in a random order.
     * @param int|Query $n
     * @return Sample
     */
    public function sample(int|Query $n): Sample
    {
        return new Sample($this, $n);
    }

    /**
     * Produce a single value from a sequence through repeated application of a reduction function.
     *
     * The reduction function can be called on:
     *
     * - two elements of the sequence
     * - one element of the sequence and one result of a previous reduction
     * - two results of previous reductions
     * The reduction function can be called on the results of two previous reductions because the reduce command is
     * distributed and parallelized across shards and CPU cores. A common mistaken when using the reduce command is to
     * suppose that the reduction is executed from left to right. Read the map-reduce in RethinkDB article to see an
     * example.
     *
     * If the sequence is empty, the server will produce a ReqlRuntimeError that can be caught with default.
     * If the sequence has only one element, the first element will be returned.
     * @param callable|Query $reductionFunction
     * @return Reduce
     */
    public function reduce(callable|Query $reductionFunction): Reduce
    {
        return new Reduce($this, $reductionFunction);
    }

    /**
     * Apply a function to a sequence in order, maintaining state via an accumulator. The fold command returns either a
     * single value or a new sequence.
     *
     * In its first form, fold operates like reduce, returning a value by applying a combining function to each element
     * in a sequence. The combining function takes two parameters: the previous reduction result (the accumulator) and
     * the current element. However, fold has the following differences from reduce:
     *
     * it is guaranteed to proceed through the sequence from first element to last.
     * it passes an initial base value to the function with the first element in place of the previous reduction result.
     * combiningFunction(accumulator | base, element) → newAccumulator
     *
     * In its second form, fold operates like concatMap, returning a new sequence rather than a single value. When an
     * emit function is provided, fold will:
     *
     * proceed through the sequence in order and take an initial base value, as above.
     * for each element in the sequence, call both the combining function and a separate emitting function. The
     * emitting function takes three parameters: the previous reduction result (the accumulator), the current element,
     * and the output from the combining function (the new value of the accumulator).
     * If provided, the emitting function must return a list.
     *
     * emit(previousAccumulator, element, accumulator) → array
     *
     * A finalEmit function may also be provided, which will be called at the end of the sequence. It takes a single
     * parameter: the result of the last reduction through the iteration (the accumulator), or the original base value
     * if the input sequence was empty. This function must return a list, which will be appended to fold’s output
     * stream.
     *
     * finalEmit(accumulator | base) → array
     * @param mixed $base
     * @param callable|Query $fun
     * @param FoldOptions $opts
     * @return Fold
     */
    public function fold(mixed $base, callable|Query $fun, FoldOptions $opts = new FoldOptions()): Fold
    {
        return new Fold($this, $base, $fun, $opts);
    }

    /**
     * Counts the number of elements in a sequence or key/value pairs in an object, or returns the size of a string or
     * binary object.
     *
     * When count is called on a sequence with a predicate value or function, it returns the number of elements in the
     * sequence equal to that value or where the function returns true. On a binary object, count returns the size of
     * the object in bytes; on strings, count returns the string’s length. This is determined by counting the number of
     * Unicode codepoints in the string, counting combining codepoints separately.
     *
     * @param mixed|null $filter
     * @return Count
     */
    public function count(mixed $filter = null): Count
    {
        return new Count($this, $filter);
    }

    /**
     * Removes duplicates from elements in a sequence.
     *
     * The distinct command can be called on any sequence or table with an index.
     *
     * While distinct can be called on a table without an index, the only effect will be to convert the table into a
     * stream; the content of the stream will not be affected.
     * @param ...$opts
     * @return Distinct
     */
    public function distinct(...$opts): Distinct
    {
        return new Distinct($this, ...$opts);
    }

    /**
     * Takes a stream and partitions it into multiple groups based on the fields or functions provided.
     *
     * With the multi flag single documents can be assigned to multiple groups, similar to the behavior of
     * multi-indexes. When multi is true and the grouping value is an array, documents will be placed in each group
     * that corresponds to the elements of the array. If the array is empty the row will be ignored.
     * @param array|string|object|callable ...$groupOn
     * @return Group
     */
    public function group(array|string|object|callable ...$groupOn): Group
    {
        return new Group($this, $groupOn);
    }

    /**
     * Takes a grouped stream or grouped data and turns it into an array of objects representing the groups. Any
     * commands chained after ungroup will operate on this array, rather than operating on each group individually.
     * This is useful if you want to e.g. order the groups by the value of their reduction.
     *
     * The format of the array returned by ungroup is the same as the default native format of grouped data in the
     * javascript driver and data explorer.
     * @return Ungroup
     */
    public function ungroup(): Ungroup
    {
        return new Ungroup($this);
    }

    /**
     * Averages all the elements of a sequence. If called with a field name, averages all the values of that field in
     * the sequence, skipping elements of the sequence that lack that field. If called with a function, calls that
     * function on every element of the sequence and averages the results, skipping elements of the sequence where that
     * function returns null or a non-existence error.
     *
     * Produces a non-existence error when called on an empty sequence. You can handle this case with default.
     * @param callable|string|null $attribute
     * @return Avg
     */
    public function avg(callable|null|string $attribute = null): Avg
    {
        return new Avg($this, $attribute);
    }

    /**
     * Sums all the elements of a sequence. If called with a field name, sums all the values of that field in the
     * sequence, skipping elements of the sequence that lack that field. If called with a function, calls that function
     * on every element of the sequence and sums the results, skipping elements of the sequence where that function
     * returns null or a non-existence error.
     *
     * Returns 0 when called on an empty sequence.
     * @param callable|string|null $attribute
     * @return Sum
     */
    public function sum(callable|null|string $attribute = null): Sum
    {
        return new Sum($this, $attribute);
    }

    /**
     * Finds the minimum element of a sequence.
     *
     * The min command can be called with:
     *
     * - a field name, to return the element of the sequence with the smallest value in that field;
     * - an index (the primary key or a secondary index), to return the element of the sequence with the smallest value
     *   in that index;
     * - a function, to apply the function to every element within the sequence and return the element which returns
     *   the smallest value from the function, ignoring any elements where the function produces a non-existence error.
     * For more information on RethinkDB’s sorting order, read the section in ReQL data types.
     *
     * Calling min on an empty sequence will throw a non-existence error; this can be handled using the default command.
     * @param string|callable ...$attributeOrOpts
     * @return Min
     */
    public function min(string|callable ...$attributeOrOpts): Min
    {
        return new Min($this, $attributeOrOpts);
    }

    /**
     * Finds the maximum element of a sequence.
     *
     * The max command can be called with:
     *
     * - a field name, to return the element of the sequence with the largest value in that field;
     * - an index (the primary key or a secondary index), to return the element of the sequence with the largest value
     *   in that index;
     * - a function, to apply the function to every element within the sequence and return the element which returns
     *   the largest value from the function, ignoring any elements where the function produces a non-existence error.
     * For more information on RethinkDB’s sorting order, read the section in ReQL data types.
     *
     * Calling max on an empty sequence will throw a non-existence error; this can be handled using the default command.
     * @param string|callable ...$attributeOrOpts
     * @return Max
     */
    public function max(string|callable ...$attributeOrOpts): Max
    {
        return new Max($this, $attributeOrOpts);
    }

    /**
     * When called with values, returns true if a sequence contains all the specified values. When called with
     * predicate functions, returns true if for each predicate there exists at least one element of the stream where
     * that predicate returns true.
     *
     * Values and predicates may be mixed freely in the argument list.
     * @param string|int|float|callable|Query ...$value
     * @return Contains
     */
    public function contains(string|int|float|callable|Query ...$value): Contains
    {
        return new Contains($this, ...$value);
    }

    /**
     * Plucks out one or more attributes from either an object or a sequence of objects (projection).
     * @param array|object|callable|string ...$attributes
     * @return Pluck
     */
    public function pluck(array|object|callable|string...$attributes): Pluck
    {
        return new Pluck($this, ...$attributes);
    }

    /**
     * The opposite of pluck; takes an object or a sequence of objects, and returns them with the specified paths
     * removed.
     * @param array|object|callable|string ...$attributes
     * @return Without
     */
    public function without(array|object|callable|string ...$attributes): Without
    {
        return new Without($this, ...$attributes);
    }

    /**
     * Merge two or more objects together to construct a new object with properties from all. When there is a conflict
     * between field names, preference is given to fields in the rightmost object in the argument list. merge also
     * accepts a subquery function that returns an object, which will be used similarly to a map function.
     * @param object|callable|array ...$other
     * @return Merge
     */
    public function merge(object|callable|array ...$other): Merge
    {
        return new Merge($this, ...$other);
    }

    /**
     * Append a value to an array.
     * @param mixed $value
     * @return Append
     */
    public function append(mixed $value): Append
    {
        return new Append($this, $value);
    }

    /**
     * Prepend a value to an array.
     * @param mixed $value
     * @return Prepend
     */
    public function prepend(mixed $value): Prepend
    {
        return new Prepend($this, $value);
    }

    /**
     * Remove the elements of one array from another array.
     * @param array|Query $value
     * @return Difference
     */
    public function difference(array|Query $value): Difference
    {
        return new Difference($this, $value);
    }

    /**
     * Add a value to an array and return it as a set (an array with distinct values).
     * @param mixed $value
     * @return SetInsert
     */
    public function setInsert(mixed $value): SetInsert
    {
        return new SetInsert($this, $value);
    }

    /**
     * Add a several values to an array and return it as a set (an array with distinct values).
     * @param array|Query $value
     * @return SetUnion
     */
    public function setUnion(array|Query $value): SetUnion
    {
        return new SetUnion($this, $value);
    }

    /**
     * Intersect two arrays returning values that occur in both of them as a set (an array with distinct values).
     * @param array|Query $value
     * @return SetIntersection
     */
    public function setIntersection(array|Query $value): SetIntersection
    {
        return new SetIntersection($this, $value);
    }

    /**
     * Remove the elements of one array from another and return them as a set (an array with distinct values).
     * @param array|Query $value
     * @return SetDifference
     */
    public function setDifference(array|Query $value): SetDifference
    {
        return new SetDifference($this, $value);
    }

    /**
     * Get a single field from an object. If called on a sequence, gets that field from every object in the sequence,
     * skipping objects that lack it.
     * @param string|Query $attribute
     * @return GetField
     */
    public function getField(string|Query $attribute): GetField
    {
        return new GetField($this, $attribute);
    }

    /**
     * Test if an object has one or more fields. An object has a field if it has that key and the key has a non-null
     * value. For instance, the object {'a': 1,'b': 2,'c': null} has the fields a and b.
     *
     * When applied to a single object, hasFields returns true if the object has the fields and false if it does not.
     * When applied to a sequence, it will return a new sequence (an array or stream) containing the elements that have
     * the specified fields.
     * @param string|Query ...$attributes
     * @return HasFields
     */
    public function hasFields(string|Query ...$attributes): HasFields
    {
        return new HasFields($this, ...$attributes);
    }

    /**
     * Insert a value in to an array at a given index. Returns the modified array.
     * @param int|Query $index
     * @param mixed $value
     * @return InsertAt
     */
    public function insertAt(int|Query $index, mixed $value): InsertAt
    {
        return new InsertAt($this, $index, $value);
    }

    /**
     * Insert several values in to an array at a given index. Returns the modified array.
     * @param int|Query $index
     * @param array|Query $value
     * @return SpliceAt
     */
    public function spliceAt(int|Query $index, array|Query $value): SpliceAt
    {
        return new SpliceAt($this, $index, $value);
    }

    /**
     * Remove one or more elements from an array at a given index. Returns the modified array. (Note: deleteAt operates
     * on arrays, not documents; to delete documents, see the delete command.)
     *
     * If only offset is specified, deleteAt removes the element at that index. If both offset and endOffset are
     * specified, deleteAt removes the range of elements between offset and endOffset, inclusive of offset but not
     * inclusive of endOffset.
     *
     * If endOffset is specified, it must not be less than offset. Both offset and endOffset must be within the array’s
     * bounds (i.e., if the array has 10 elements, an offset or endOffset of 10 or higher is invalid).
     *
     * By using a negative offset you can delete from the end of the array. -1 is the last element in the array, -2 is
     * the second-to-last element, and so on. You may specify a negative endOffset, although just as with a positive
     * value, this will not be inclusive. The range (2,-1) specifies the third element through the next-to-last element.
     * @param int|Query $index
     * @param int|Query|null $endIndex
     * @return DeleteAt
     */
    public function deleteAt(int|Query $index, int|Query|null $endIndex = null): DeleteAt
    {
        return new DeleteAt($this, $index, $endIndex);
    }

    /**
     * Change a value in an array at a given index. Returns the modified array.
     * @param int|Query $index
     * @param mixed $value
     * @return ChangeAt
     */
    public function changeAt(int|Query $index, mixed $value): ChangeAt
    {
        return new ChangeAt($this, $index, $value);
    }

    /**
     * Return an array containing all of an object’s keys. Note that the keys will be sorted as described in ReQL data
     * types (for strings, lexicographically).
     *
     * @return Keys
     */
    public function keys(): Keys
    {
        return new Keys($this);
    }

    /**
     * Return an array containing all of an object’s values. values() guarantees the values will come out in the same
     * order as keys.
     * @return Values
     */
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

    /**
     * Matches against a regular expression. If there is a match, returns an object with the fields:
     *
     * str: The matched string
     * start: The matched string’s start
     * end: The matched string’s end
     * groups: The capture groups defined with parentheses
     * If no match is found, returns null.
     *
     * Accepts RE2 syntax. You can enable case-insensitive matching by prefixing the regular expression with (?i). See
     * the linked RE2 documentation for more flags.
     *
     * The match command does not support backreferences.
     * @param string|Query $expression
     * @return RqlMatch
     */
    public function match(string|Query $expression): RqlMatch
    {
        return new RqlMatch($this, $expression);
    }

    /**
     * Uppercases a string.
     * @return Upcase
     */
    public function upcase(): Upcase
    {
        return new Upcase($this);
    }

    /**
     * Lowercases a string.
     * @return Downcase
     */
    public function downcase(): Downcase
    {
        return new Downcase($this);
    }

    /**
     * Splits a string into substrings. Splits on whitespace when called with no arguments. When called with a
     * separator, splits on that separator. When called with a separator and a maximum number of splits, splits on that
     * separator at most max_splits times. (Can be called with null as the separator if you want to split on whitespace
     * while still specifying max_splits.)
     *
     * Mimics the behavior of Python’s string.split in edge cases, except for splitting on the empty string, which
     * instead produces an array of single-character strings.
     * @param string|Query|null $separator
     * @param int|Query|null $maxSplits
     * @return Split
     */
    public function split(string|Query|null $separator = null, int|Query|null $maxSplits = null): Split
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

    /**
     * Loop over a sequence, evaluating the given write query for each element.
     * @param callable|Query $queryFunction
     * @return RForeach
     */
    public function rForeach(callable|Query $queryFunction): RForeach
    {
        return new RForeach($this, $queryFunction);
    }

    /**
     * Convert a value of one type into another.
     *
     * a sequence, selection or object can be coerced to an array
     * a sequence, selection or an array of key-value pairs can be coerced to an object
     * a string can be coerced to a number
     * any datum (single value) can be coerced to to a string
     * a binary object can be coerced to a string and vice-versa
     * @param string|Query $typeName
     * @return CoerceTo
     */
    public function coerceTo(string|Query $typeName): CoerceTo
    {
        return new CoerceTo($this, $typeName);
    }

    /**
     * Gets the type of a ReQL query’s return value.
     *
     * The type will be returned as a string:
     *
     * ARRAY
     * BOOL
     * DB
     * FUNCTION
     * GROUPED_DATA
     * GROUPED_STREAM
     * MAXVAL
     * MINVAL
     * NULL
     * NUMBER
     * OBJECT
     * PTYPE<BINARY>
     * PTYPE<GEOMETRY>
     * PTYPE<TIME>
     * SELECTION<ARRAY>
     * SELECTION<OBJECT>
     * SELECTION<STREAM>
     * STREAM
     * STRING
     * TABLE_SLICE
     * TABLE
     * Read the article on ReQL data types for a more detailed discussion. Note that some possible return values from
     * typeOf are internal values, such as MAXVAL, and unlikely to be returned from queries in standard practice.
     * @return TypeOf
     */
    public function typeOf(): TypeOf
    {
        return new TypeOf($this);
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
     * @param Query|callable $inExpr
     * @return RDo
     */
    public function rDo(Query|callable $inExpr): RDo
    {
        return new RDo($this, $inExpr);
    }

    /**
     * Convert a time object to its epoch time.
     * @return ToEpochTime
     */
    public function toEpochTime(): ToEpochTime
    {
        return new ToEpochTime($this);
    }

    /**
     * Convert a time object to a string in ISO 8601 format.
     * @return ToIso8601
     */
    public function toIso8601(): ToIso8601
    {
        return new ToIso8601($this);
    }

    /**
     * Return a new time object with a different timezone. While the time stays the same, the results returned by
     * methods such as hours() will change since they take the timezone into account. The timezone argument has to be
     * of the ISO 8601 format.
     * @param string|Query $timezone
     * @return InTimezone
     */
    public function inTimezone(string|Query $timezone): InTimezone
    {
        return new InTimezone($this, $timezone);
    }

    /**
     * Return the timezone of the time object.
     * @return Timezone
     */
    public function timezone(): Timezone
    {
        return new Timezone($this);
    }

    /**
     * Return whether a time is between two other times.
     *
     * By default, this is inclusive of the start time and exclusive of the end time. Set leftBound and rightBound to
     * explicitly include (closed) or exclude (open) that endpoint of the range.
     * @param Query $startTime
     * @param Query $endTime
     * @param SliceOptions $opts
     * @return During
     */
    public function during(Query $startTime, Query $endTime, SliceOptions $opts = new SliceOptions()): During
    {
        return new During($this, $startTime, $endTime, $opts);
    }

    /**
     * Return a new time object only based on the day, month and year (ie. the same day at 00:00).
     * @return Date
     */
    public function date(): Date
    {
        return new Date($this);
    }

    /**
     * Return the number of seconds elapsed since the beginning of the day stored in the time object.
     * @return TimeOfDay
     */
    public function timeOfDay(): TimeOfDay
    {
        return new TimeOfDay($this);
    }

    /**
     * Return the year of a time object.
     * @return Year
     */
    public function year(): Year
    {
        return new Year($this);
    }

    /**
     * Return the month of a time object as a number between 1 and 12. For your convenience, the terms r.january,
     * r.february etc. are defined and map to the appropriate integer.
     * @return Month
     */
    public function month(): Month
    {
        return new Month($this);
    }

    /**
     * Return the day of a time object as a number between 1 and 31.
     * @return Day
     */
    public function day(): Day
    {
        return new Day($this);
    }

    /**
     * Return the day of week of a time object as a number between 1 and 7 (following ISO 8601 standard). For your
     * convenience, the terms r.monday, r.tuesday etc. are defined and map to the appropriate integer.
     * @return DayOfWeek
     */
    public function dayOfWeek(): DayOfWeek
    {
        return new DayOfWeek($this);
    }

    /**
     * Return the day of the year of a time object as a number between 1 and 366 (following ISO 8601 standard).
     * @return DayOfYear
     */
    public function dayOfYear(): DayOfYear
    {
        return new DayOfYear($this);
    }

    /**
     * Return the hour in a time object as a number between 0 and 23.
     * @return Hours
     */
    public function hours(): Hours
    {
        return new Hours($this);
    }

    /**
     * Return the minute in a time object as a number between 0 and 59.
     * @return Minutes
     */
    public function minutes(): Minutes
    {
        return new Minutes($this);
    }

    /**
     * Return the seconds in a time object as a number between 0 and 59.999 (double precision).
     * @return Seconds
     */
    public function seconds(): Seconds
    {
        return new Seconds($this);
    }

    /**
     * Turn a query into a changefeed, an infinite stream of objects representing changes to the query’s results as
     * they occur. A changefeed may return changes to a table or an individual document (a “point” changefeed).
     * Commands such as filter or map may be used before the changes command to transform or filter the output, and
     * many commands that operate on sequences can be chained after changes.
     * @param ChangesOptions $opts
     * @return Changes
     */
    public function changes(ChangesOptions $opts = new ChangesOptions()): Changes
    {
        return new Changes($this, $opts);
    }

    /**
     * Convert a ReQL geometry object to a GeoJSON object.
     * @return ToGeoJSON
     */
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

    /**
     * Convert a Line object into a Polygon object. If the last point does not specify the same coordinates as the
     * first point, polygon will close the polygon by connecting them.
     *
     * Longitude (−180 to 180) and latitude (−90 to 90) of vertices are plotted on a perfect sphere. See Geospatial
     * support for more information on ReQL’s coordinate system.
     *
     * If the last point does not specify the same coordinates as the first point, polygon will close the polygon by
     * connecting them. You cannot directly construct a polygon with holes in it using polygon, but you can use
     * polygonSub to use a second polygon within the interior of the first to define a hole.
     * @return Fill
     */
    public function fill(): Fill
    {
        return new Fill($this);
    }

    /**
     * Use polygon2 to “punch out” a hole in polygon1. polygon2 must be completely contained within polygon1 and must
     * have no holes itself (it must not be the output of polygonSub itself).
     * @param Query $other
     * @return PolygonSub
     */
    public function polygonSub(Query $other): PolygonSub
    {
        return new PolygonSub($this, $other);
    }

    /**
     * Convert a ReQL value or object to a JSON string. You may use either toJsonString or toJSON.
     * @return ToJsonString
     */
    public function toJsonString(): ToJsonString
    {
        return new ToJsonString($this);
    }
}
