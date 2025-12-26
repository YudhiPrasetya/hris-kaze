<?php
/**
 * This file is part of the SolidWorx Lodash-PHP project.
 *
 * @author     Pierre du Plessis <open-source@solidworx.co>
 * @copyright  Copyright (c) 2017
 */

declare(strict_types=1);

/**
 * Class _
 *
 ** Array
 * @method static array chunk(?array $array, int $number)
 * @method static array compact(?array $array)
 * @method static array concat($array, ...$values)
 * @method static array difference(array $array, array ...$values)
 * @method static array differenceBy(array $array, ...$values)
 * @method static array differenceWith(array $array, ...$values)
 * @method static array drop(array $array, int $n = 1)
 * @method static array dropRight(array $array, int $n = 1)
 * @method static array dropRightWhile(array $array, callable $predicate)
 * @method static array dropWhile(array $array, callable $predicate)
 * @method static int findIndex(array $array, $predicate, int $fromIndex = null)
 * @method static int findLastIndex(array $array, $predicate, int $fromIndex = null)
 * @method static array flatten(array $array = null)
 * @method static array flattenDeep(array $array)
 * @method static array flattenDepth(array $array, int $depth = 1)
 * @method static void fromPairs(array $pairs)
 * @method static void head(array $array)
 * @method static int indexOf(array $array, $value, int $fromIndex = null)
 * @method static array initial(array $array)
 * @method static array intersection(array ...$arrays)
 * @method static array intersectionBy(...$arrays)
 * @method static array intersectionWith(...$arrays)
 * @method static void last(array $array)
 * @method static int lastIndexOf(array $array, $value, int $fromIndex = null)
 * @method static void nth(array $array, int $n)
 * @method static array pull(array &$array, ...$values)
 * @method static array pullAll(array &$array, array $values)
 * @method static array pullAllBy(array &$array, array $values, $iteratee)
 * @method static array pullAllWith(array &$array, array $values, callable $comparator)
 * @method static array pullAt(array &$array, $indexes)
 * @method static array remove(array &$array, callable $predicate)
 * @method static array slice(array $array, int $start, int $end = null)
 * @method static array tail(array $array)
 * @method static array take(array $array, int $n = 1)
 * @method static array takeRight(array $array, int $n = 1)
 * @method static array takeRightWhile(array $array, $predicate)
 * @method static array takeWhile(array $array, $predicate)
 * @method static array union(array ...$arrays)
 * @method static array unionBy(...$arrays)
 * @method static array unionWith(...$arrays)
 * @method static array uniq(array $array = [])
 * @method static array uniqBy(array $array, $iteratee)
 * @method static array uniqWith(array $array, callable $comparator)
 * @method static array unzip(array $array)
 * @method static array unzipWith(array $array, ?callable $iteratee = null)
 * @method static array without(array $array, ...$values)
 * @method static array zip(array ...$arrays)
 * @method static void zipObject(array $props = [], array $values = [])
 * @method static void zipObjectDeep(array $props = [], array $values = [])
 * @method static array zipWith(...$arrays)
 *
 ** Collection
 * @method static array countBy(iterable $collection, callable $iteratee)
 * @method static void each($collection, callable $iteratee)
 * @method static void eachRight($collection, callable $iteratee)
 * @method static bool every(iterable $collection, $predicate)
 * @method static array filter(iterable $array, $predicate = null)
 * @method static void find(iterable $collection, $predicate = null, int $fromIndex = 0)
 * @method static void findLast(iterable $collection, $predicate = null, int $fromIndex = 0)
 * @method static void duplicate($n)
 * @method static array groupBy(iterable $collection, $iteratee)
 * @method static array invokeMap(iterable $collection, $path, array $args = [])
 * @method static array keyBy(iterable $collection, $iteratee)
 * @method static void square(int $n)
 * @method static array orderBy(?iterable $collection, array $iteratee, array $orders)
 * @method static array partition(iterable $collection, $predicate = null)
 * @method static void reduce(iterable $collection, $iteratee, $accumulator = null)
 * @method static void reduceRight(iterable $collection, $iteratee, $accumulator = null)
 * @method static array reject(iterable $collection, $predicate = null)
 * @method static void sample(array $array)
 * @method static array sampleSize(array $array, int $n = 1)
 * @method static array shuffle(array $array = [])
 * @method static int size($collection)
 * @method static bool some(iterable $collection, $predicate = null)
 * @method static array sortBy($collection, $iteratees)
 *
 ** Date
 * @method static int now()
 *
 ** Function
 * @method static callable after(int $n, callable $func)
 * @method static callable ary(callable $func, int $n)
 * @method static callable before(int $n, callable $func)
 * @method static void greet($greeting, $punctuation)
 * @method static void curry(callable $func, ?int $arity = null)
 * @method static int delay(callable $func, int $wait = 1, ...$args)
 * @method static callable flip(callable $func)
 * @method static void memoize(callable $func, callable $resolver = null)
 * @method static void isEven($n)
 * @method static callable once(callable $func)
 * @method static void doubled($n)
 * @method static callable rest(callable $func, ?int $start = null)
 * @method static void spread(callable $func, ?int $start = null)
 * @method static callable unary(callable $func)
 * @method static callable wrap($value, callable $wrapper = null)
 *
 ** Lang
 * @method static bool eq($value, $other)
 * @method static bool isEqual($value, $other)
 * @method static bool isError($value)
 *
 ** Math
 * @method static void add($augend, $addend)
 * @method static void max(?array $array)
 * @method static void maxBy(?array $array, $iteratee)
 *
 ** Number
 * @method static int clamp(int $number, int $lower, int $upper)
 * @method static bool inRange(float $number, float $start = 0, float $end = 0)
 * @method static void random($lower = null, $upper = null, $floating = null)
 *
 ** Object
 * @method static void get($object, $path, $defaultValue = null)
 * @method static void pick($object, $paths)
 * @method static void pickBy($object, callable $predicate)
 *
 ** Seq
 * @method static void chain($value)
 *
 ** String
 * @method static string camelCase(string $string)
 * @method static string capitalize(string $string)
 * @method static string deburr(string $string)
 * @method static bool endsWith(string $string, string $target, int $position = null)
 * @method static void escape(string $string)
 * @method static string escapeRegExp(string $string)
 * @method static void kebabCase(string $string)
 * @method static void lowerCase(string $string)
 * @method static string lowerFirst(string $string)
 * @method static string pad(string $string, int $length, string $chars = ' ')
 * @method static string padEnd(string $string, int $length, string $chars = ' ')
 * @method static string padStart(string $string, int $length, string $chars = ' ')
 * @method static int parseInt($string, int $radix = null)
 * @method static string repeat(string $string, int $n = 1)
 * @method static string replace(string $string, string $pattern, $replacement = null)
 * @method static string snakeCase(string $string)
 * @method static array split(string $string, string $separator, int $limit = 0)
 * @method static void startCase(string $string)
 * @method static bool startsWith(string $string, string $target, int $position = null)
 * @method static callable template(string $string, array $options = [])
 * @method static string toLower(string $string)
 * @method static string toUpper(string $string)
 * @method static string trim(string $string, string $chars = ' ')
 * @method static string trimEnd(string $string, string $chars = ' ')
 * @method static string trimStart(string $string, string $chars = ' ')
 * @method static void truncate($string, array $options = [])
 * @method static string unescape(string $string)
 * @method static void upperCase(string $string)
 * @method static string upperFirst(string $string)
 * @method static array words(string $string, string $pattern = null)
 *
 ** Util
 * @method static void attempt(callable $func, ...$args)
 * @method static void defaultTo($value, $defaultValue)
 * @method static void identity($value = null)
 * @method static callable property($path)
 *
 */
final class _ {
	public const reInterpolate = '<%=([\s\S]+?)%>';

	public const reEvaluate = "<%([\s\S]+?)%>";

	public const reEscape = "<%-([\s\S]+?)%>";

	public static $templateSettings = [

		/**
		 * Used to detect `data` property values to be HTML-escaped.
		 */
		'escape'      => self::reEscape,

		/**
		 * Used to detect code to be evaluated.
		 */
		'evaluate'    => self::reEvaluate,

		/**
		 * Used to detect `data` property values to inject.
		 */
		'interpolate' => self::reInterpolate,

		/**
		 * Used to import functions or classes into the compiled template.
		 */
		'imports'     => [

			/**
			 * A reference to the `lodash` escape function.
			 */
			'_\escape' => '__e',
		],
	];

	public $__chain__ = false;

	private $value;

	public function __construct($value) {
		$this->value = $value;
	}

	/**
	 * @param $method
	 * @param $arguments
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function __call($method, $arguments) {
		$this->value = self::__callStatic($method, \array_merge([$this->value], $arguments));

		return $this;
	}

	/**
	 * @param string $method
	 * @param mixed  $args
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function __callStatic(string $method, $args) {
		if (!\is_callable("_\\$method")) {
			throw new \InvalidArgumentException("Function _::$method is not valid");
		}

		$ret = ("_\\$method")(...$args);

		// Special case
		if (is_array($ret)) return new ArrayObject($ret);

		return $ret;
	}

	public function value() {
		return $this->value;
	}
}

function lodash($value): _ {
	return new _($value);
}

// We can't use "_" as a function name, since it collides with the "_" function in the gettext extension
// Laravel uses a function __, so only register the alias if the function name is not in use
if (!function_exists('__')) {
	function __($value): _ {
		return new _($value);
	}
}

if (!defined('_')) {
	define('_', _::class);
}
