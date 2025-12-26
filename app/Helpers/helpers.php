<?php
// __

/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   helpers.php
 * @date   2020-10-30 5:40:29
 */

use App\Exceptions\SchemaNotFoundException;
use App\Libraries\UUID;
use App\Models\ModelBase;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\View\ComponentAttributeBag;
use MaxMind\Db\Reader\InvalidDatabaseException;


if (!function_exists('client_city')) {
	/**
	 * Get client city
	 *
	 * @return \GeoIp2\Model\City
	 * @throws \GeoIp2\Exception\AddressNotFoundException
	 * @throws \MaxMind\Db\Reader\InvalidDatabaseException
	 */
	function client_city(): City {
		try {
			$remoteAddr = Request::server('REMOTE_ADDR');
			$reader = new Reader(resource_path('etc/maxmind/GeoLite2-City.mmdb'));

			return $reader->city($remoteAddr);
		}
		catch (InvalidDatabaseException $e) {
			throw new InvalidDatabaseException($e->getMessage(), $e->getCode());
		}
		catch (AddressNotFoundException $e) {
			// This API should be called on client side
			$rsp = Http::asJson()->get('https://api.myip.com');
			$rsp = json_decode($rsp->body());

			return $reader->city($rsp->ip);
		}
	}
}

if (!function_exists('client_datetime')) {
	/**
	 * Get client datetime
	 *
	 * @return \DateTime
	 * @throws \GeoIp2\Exception\AddressNotFoundException
	 * @throws \MaxMind\Db\Reader\InvalidDatabaseException
	 */
	function client_datetime(): DateTime {
		$city = client_city();
		$dt = new DateTime();
		$dt->setTimezone(new DateTimeZone($city->location->timeZone));

		return $dt;
	}
}

if (!function_exists('array_remove_null_callback')) {
	/**
	 * This is callback function used by array_filter
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	function array_remove_null_callback($value): bool {
		return !empty(trim($value));
	}
}

if (!function_exists('parse_html')) {
	function parse_html(string $slot): ?DOMDocument {
		$doc = null;

		if (strlen($slot)) {
			$doc = new DOMDocument();
			$doc->loadHTML($slot);
		}

		return $doc;
	}
}

if (!function_exists('child_nodes')) {
	function child_nodes(DOMDocument $doc): DOMNodeList {
		return $doc->childNodes->item(1)->firstChild->childNodes;
	}
}

if (!function_exists('add_class')) {
	function add_class(DOMElement $el, string $class): bool {
		$classes = explode(' ', $el->getAttribute('class'));

		if (!in_array($class, $classes)) {
			$classes[] = $class;
			$el->setAttribute('class', trim(implode(' ', $classes)));

			return true;
		}

		return false;
	}
}

if (!function_exists('init_form_control')) {
	function init_form_control(DOMNode $node): bool {
		$classes = [
			'form-control'           => [
				'input'    => [
					'text',
					'email',
					'password',
					'color',
					'button',
					'date',
					'datetime-local',
					'month',
					'number',
					'tel',
					'time',
					'url',
					'week',
				],
				'textarea' => [],
				'select'   => [],
			],
			'form-control-range'     => ['input' => ['range']],
			'form-control-plaintext' => ['input' => ['file']],
			'form-check-input'       => ['input' => ['checkbox', 'radio']],
		];

		if ($node->nodeType == XML_ELEMENT_NODE) {
			foreach ($classes as $class => $controls) {
				if (array_key_exists($node->tagName, $controls)) {
					if ($node->hasAttribute('type') && $node->tagName == 'input') {
						$type = $node->getAttribute('type');
						if (in_array($type, $controls[$node->tagName])) {
							add_class($node, $class);

							return true;
						}
					}
					else {
						add_class($node, $class);

						return true;
					}
				}
			}
		}

		return false;
	}
}

if (!function_exists('split_classes')) {
	function split_classes(string $classes): array {
		$re = '/[\s]+/m';
		while (strpos(trim($classes), ' ') !== false) {
			$classes = preg_replace($re, ',', $classes);
		}

		return array_filter(explode(',', trim($classes, ', ')), 'array_remove_null_callback');
	}
}

if (!function_exists('get_classes')) {
	/**
	 * @param ComponentAttributeBag|\DOMElement $attributes
	 *
	 * @return array
	 */
	function get_classes($attributes): array {
		$classes = null;

		if (is_a($attributes, 'ComponentAttributeBag') || $attributes instanceof ComponentAttributeBag) {
			$classes = $attributes->get('class');
			$classes = is_array($classes) ? implode(' ', $classes) : $classes;
		}
		if (is_a($attributes, 'DOMElement') || $attributes instanceof DOMElement)
			$classes = $attributes->getAttribute('class');

		$classes = (empty(trim($classes)) ? '' : trim($classes));

		return split_classes($classes);
	}
}

if (!function_exists('array_flatten')) {
	/**
	 * Remove duplicate and convert into 1 dimension array
	 *
	 * @param mixed ...$needles
	 *
	 * @return array
	 */
	function array_flatten(...$needles): array {
		$_ = [];

		foreach ($needles as $needle) {
			if (empty($needle)) continue;

			if (is_string($needle)) {
				$nn = explode(',', $needle);
				foreach ($nn as $n) {
					if (!in_array($n, $_) || !empty($n))
						$_[] = trim($n);
				}
			}
			elseif (is_array($needle)) {
				$_ = array_merge($_, array_flatten(...$needle));
			}
			else {
				if (!in_array($needle, $_))
					$_[] = trim($needle);
			}
		}

		return $_;
	}
}

if (!function_exists('array_remove_duplicate')) {
	function array_remove_duplicate(array $current, ...$classes): array {
		foreach ($classes as $class) {
			if (($key = array_search($class, $current)) !== false) {
				unset($current[$key]);
			}
			else {
				$current[] = $class;
			}
		}

		return $current;
	}
}

if (!function_exists('remove_duplicate_class')) {
	function remove_duplicate_class(ComponentAttributeBag $attributes, ...$classes) {
		$current = array_remove_duplicate(get_classes($attributes), ...$classes);
		$attributes->offsetSet('class', implode(' ', $current));
	}
}

if (!function_exists('remove_class')) {
	function remove_class(array $haystack, ...$classes): array {
		foreach ($haystack as $k => $class) {
			if (in_array($class, $classes))
				unset($haystack[$k]);
		}

		return array_flatten($haystack);
	}
}

if (!function_exists('array_prepend')) {
	function array_prepend(array $haystack, ...$needles): array {
		$temp = $haystack;
		$_ = array_flatten(...$needles);

		foreach ($_ as $k => $__) {
			if (in_array($__, $temp) || !empty($n))
				unset($_[$k]);
		}

		return array_merge($_, $temp);
	}
}

if (!function_exists('array_append')) {
	function array_append(array $haystack, ...$needles): array {
		$temp = $haystack;
		$_ = array_flatten(...$needles);

		foreach ($_ as $k => $__) {
			if (in_array($__, $temp) || !empty($n))
				unset($_[$k]);
		}

		return array_merge($temp, $_);
	}
}

if (!function_exists('array_remove_empty')) {
	/**
	 * @param array|Collection $array
	 *
	 * @return array|Collection
	 */
	function array_remove_empty($array) {
		$t = collect($array);
		foreach ($t as $k => &$v) {
			$v = trim($v);
			if (empty($v)) $t->offsetUnset($k);
		}

		return $t->toArray();
	}
}

if (!function_exists('class_prepend')) {
	function class_prepend(array $haystack, ...$needles): ?string {
		return implode(' ', array_prepend($haystack, ...$needles));
	}
}

if (!function_exists('class_append')) {
	function class_append(array $haystack, ...$needles): ?string {
		return implode(' ', array_append($haystack, ...$needles));
	}
}

if (!function_exists('datetime_to_human_readable')) {
	/**
	 * @param \DateTime|null $date
	 *
	 * @return \DateTime
	 * @throws \GeoIp2\Exception\AddressNotFoundException
	 * @throws \MaxMind\Db\Reader\InvalidDatabaseException
	 * @throws \Exception
	 */
	function date_converter(DateTime $date = null): DateTime {
		$clientDateTime = client_datetime();
		$timezone = $clientDateTime->getTimezone();

		// immutable dates
		if ($date instanceof DateTimeImmutable) {
			return false !== $timezone ? $date->setTimezone($timezone) : $date;
		}

		if ($date instanceof DateTimeInterface) {
			$date = clone $date;
			if (false !== $timezone) $date->setTimezone($timezone);

			return $date;
		}

		if (null === $date || 'now' === $date) {
			return new DateTime($date, $timezone);
		}

		$asString = (string)$date;
		if (ctype_digit($asString) ||
		    (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
			$date = new DateTime('@' . $date);
		}
		else {
			$date = new DateTime($date, $timezone);
		}

		if (false !== $timezone) $date->setTimezone($timezone);

		return $date;
	}
}

if (!function_exists('getPluralizedInterval')) {
	function getPluralizedInterval(int $count, string $invert, string $unit): string {
		if (1 !== $count) {
			$unit .= 's';
		}

		return $invert ? "in $count $unit" : "$count $unit ago";
	}
}

if (!function_exists('timeDiff')) {
	/**
	 * @param \DateTime $date
	 * @param null      $now
	 *
	 * @return string
	 * @throws \GeoIp2\Exception\AddressNotFoundException
	 * @throws \MaxMind\Db\Reader\InvalidDatabaseException
	 */
	function timeDiff(DateTime $date, $now = null): string {
		$units = array(
			'y' => 'year',
			'm' => 'month',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);

		// Convert both dates to DateTime instances.
		$date = date_converter($date);
		$now = date_converter($now);

		// Get the difference between the two DateTime objects.
		$diff = $date->diff($now);

		// Check for each interval if it appears in the $diff object.
		foreach ($units as $attribute => $unit) {
			$count = $diff->$attribute;

			if (0 !== $count) {
				return getPluralizedInterval($count, $diff->invert, $unit);
			}
		}

		return '';
	}
}

if (!function_exists('path')) {
	function path(...$paths): string {
		return implode(DIRECTORY_SEPARATOR, $paths);
	}
}

if (!function_exists('getModelForGuard')) {
	/**
	 * @param string $guard
	 *
	 * @return mixed
	 */
	function getModelForGuard(string $guard) {
		return collect(config('auth.guards'))
			->map(
				function ($guard) {
					if (!isset($guard['provider'])) {
						return;
					}

					return config("auth.providers.{$guard['provider']}.model");
				}
			)->get($guard);
	}
}

if (!function_exists('uuid_v3')) {
	function uuid_v3(string $namespace, string $name): string {
		return UUID::v3($namespace, $name);
	}
}

if (!function_exists('connection')) {
	/**
	 * @param string $name
	 *
	 * @return string
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	function connection(string $name): string {
		$tables = config('settings.tables');
		if (!isset($tables[$name])) throw new SchemaNotFoundException($name);

		return $tables[$name]['connection'];
	}
}

if (!function_exists('table')) {
	/**
	 * @param string      $name
	 *
	 * @param string|null $alias
	 * @param bool        $onlyName
	 *
	 * @return string
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	function table(string $name, ?string $alias = null, bool $onlyName = false): string {
		$schema = substr($name, 0, strpos($name, '.'));
		$table = substr($name, strpos($name, '.') + 1);
		$tables = config('settings.tables');

		if (!isset($tables[$schema])) throw new SchemaNotFoundException($schema);
		if ($onlyName) return $table . ($alias ? " as $alias" : '');

		return trim($tables[$schema]['schema'] . ".$table" . ($alias ? " as $alias" : ''));
	}
}

if (!function_exists('QR')) {
	/**
	 * @return \Illuminate\Database\ConnectionInterface
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	function QR(): ConnectionInterface {
		return DB::connection(connection('qr'));
	}
}

if (!function_exists('CreditDebit')) {
	/**
	 * @return \Illuminate\Database\ConnectionInterface
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	function CreditDebit(): ConnectionInterface {
		return DB::connection(connection('credit_debit'));
	}
}

if (!function_exists('Master')) {
	/**
	 * @return \Illuminate\Database\ConnectionInterface
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	function Master(): ConnectionInterface {
		return DB::connection(connection('master'));
	}
}

if (!function_exists('get_table')) {
	function get_table(string $table, string $as = null): Builder {
		$schema = substr($table, 0, strpos($table, '.'));
		$table = substr($table, strpos($table, '.') + 1);

		return (function ($schema) {
			switch (strtolower($schema)) {
				case 'qr':
					return QR();
				case 'credit_debit':
					return CreditDebit();
				case 'master':
				default:
					return Master();
			}
		})($schema)->table($table, $as);
	}
}

if (!function_exists('merge_attributes')) {
	/**
	 * @param ComponentAttributeBag $attr
	 * @param string|array          $key
	 * @param mixed                 $value
	 */
	function merge_attributes(ComponentAttributeBag &$attr, $key, $value = null) {
		if (is_array($key)) {
			$key = array_remove_empty($key);
			$attr = $attr->merge($key);
		}

		if (is_string($key) && !empty($value)) {
			if (is_array($value)) $value = implode(' ', $value);
			$attr = $attr->merge(["$key" => trim("$value")]);
		}
	}
}

if (!function_exists('parse_date_from_format')) {
	function parse_date_from_format(string $format, string $date, bool $toDateObject = false) {
		$d = DateTime::createFromFormat($format, $date);

		return $toDateObject ? $d : $d->format('Y-m-d H:i:s');
	}
}

if (!function_exists('crypto_rand_secure')) {
	function crypto_rand_secure($min, $max): int {
		$range = $max - $min;
		if ($range < 1) return $min;        // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int)($log / 8) + 1;       // length in bytes
		$bits = (int)$log + 1;              // length in bits
		$filter = (int)(1 << $bits) - 1;    // set all lower bits to 1

		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter;          // discard irrelevant bits
		} while ($rnd > $range);

		return $min + $rnd;
	}
}

if (!function_exists('uniq_string')) {
	function uniq_string(int $length = 64): string {
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet .= "0123456789";
		$max = strlen($codeAlphabet); // edited

		for ($i = 0; $i < $length; $i++) {
			$token .= $codeAlphabet[crypto_rand_secure(0, $max - 1)];
		}

		return $token;
	}
}

if (!function_exists('safe_call')) {
	function safe_call(&$obj, string $chain, $default = null) {
		$chains = explode('.', $chain);
		$current = $obj;

		if (method_exists($current, $chains[0])) {
			$ret = $current->{$chains[0]}();
			$less = array_slice($chains, 1);

			if (is_null($ret)) return $default;
			if (count($less) > 0) $ret = safe_call($ret, implode('.', array_slice($chains, 1)));

			return $ret;
		}
		else if ($current instanceof ModelBase || $current instanceof Model) {
			$ret = $current->{$chains[0]};

			if (is_null($ret)) return $default;

			return $ret;
		}
		else if (property_exists($current, $chains[0])) {
			$ret = $current->{$chains[0]};
			$less = array_slice($chains, 1);

			if (is_null($ret)) return $default;
			if (count($less) > 0) $ret = safe_call($ret, implode('.', array_slice($chains, 1)));

			return $ret;
		}
		else {
			return $default;
		}
	}
}

if (!function_exists('formatBytes')) {
	/**
	 * @param int $bytes     Number of bytes (eg. 25907)
	 * @param int $precision [optional] Number of digits after the decimal point (eg. 1)
	 *
	 * @return string Value converted with unit (eg. 25.3KB)
	 */
	function formatBytes($size, $precision = 2): string {
		$unit = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

		for ($i = 0; $size >= 1024 && $i < count($unit) - 1; $i++) {
			$size /= 1024;
		}

		return round($size, $precision) . ' ' . $unit[$i];
	}
}

if (!function_exists('is_date')) {
	function is_date($datetime): bool {
		if (empty($datetime) || !is_string($datetime)) return false;
		$ret = date_parse($datetime);

		return $ret['error_count'] == 0 && $ret['warning_count'] == 0 && !empty($ret['year']) && !empty($ret['month']) && !empty($ret['day']);
	}
}

if (!function_exists('is_time')) {
	function is_time($datetime): bool {
		if (empty($datetime) || !is_string($datetime)) return false;
		$ret = date_parse($datetime);

		return $ret['error_count'] == 0 && $ret['warning_count'] == 0 && !empty($ret['hour']) && !empty($ret['minute']) && !empty($ret['second']);
	}
}

if (!function_exists('is_datetime')) {
	function is_datetime($datetime): bool {
		if (empty($datetime) || !is_string($datetime)) return false;
		$ret = date_parse($datetime);

		return $ret['error_count'] == 0 && $ret['warning_count'] == 0 && !empty($ret['year']) && !empty($ret['month']) && !empty($ret['day']) &&
		       !empty($ret['hour']) && !empty($ret['minute']) && !empty($ret['second']);
	}
}

if (!function_exists('parse_date')) {
	/**
	 * @param string $datetime
	 *
	 * @return \DateTime|false
	 * @throws \Exception
	 */
	function parse_date($datetime) {
		return is_date($datetime) ? new DateTime($datetime) : false;
	}
}

if (!function_exists('is_previous_route')) {
	/**
	 * @param string $route
	 *
	 * @return string
	 */
	function is_previous_route(string $route): string {
		$previousRequest = app('request')->create(URL::previous());

		return app('router')->getRoutes()->match($previousRequest)->getName() === $route;
	}
}

if (!function_exists('is_current_route')) {
	/**
	 * @param string $route
	 *
	 * @return string
	 */
	function is_current_route(string $route): string {
		$previousRequest = app('request')->create(URL::current());

		return app('router')->getRoutes()->match($previousRequest)->getName() === $route;
	}
}

if (!function_exists('get_route')) {
	/**
	 * @param string|null $url
	 *
	 * @return string
	 */
	function get_route(?string $url = null): string {
		try {
			if (empty($url)) {
				$request = app('request')->create(URL::current());
			}
			else {
				$request = app('request')->create($url);
			}

			return app('router')->getRoutes()->match($request)->getName();
		}
		catch (\Exception $e) {
			return "";
		}
	}
}

if (!function_exists('get_route')) {
	/**
	 * @param string|null $url
	 *
	 * @return string
	 */
	function route_exists(?string $name = null): string {
		return app('router')->getRoutes()->getByName($name) !== null;
	}
}

if (!function_exists('is_devel')) {
	function is_devel(): bool {
		return in_array(env('APP_ENV'), ['development', 'local', 'devel']);
	}
}

if (!function_exists('get_locales')) {
	function get_locales(): array {
		return [
			"ar_JO",
			"ar_SA",
			"at_AT",
			"bg_BG",
			"bn_BD",
			"cs_CZ",
			"da_DK",
			"de_AT",
			"de_CH",
			"de_DE",
			"el_CY",
			"el_GR",
			"en_AU",
			"en_CA",
			"en_GB",
			"en_HK",
			"en_IN",
			"en_NG",
			"en_NZ",
			"en_PH",
			"en_SG",
			"en_UG",
			"en_US",
			"en_ZA",
			"es_AR",
			"es_ES",
			"es_PE",
			"es_VE",
			"et_EE",
			"fa_IR",
			"fi_FI",
			"fr_BE",
			"fr_CA",
			"fr_CH",
			"fr_FR",
			"he_IL",
			"hr_HR",
			"hu_HU",
			"hy_AM",
			"id_ID",
			"is_IS",
			"it_CH",
			"it_IT",
			"ja_JP",
			"ka_GE",
			"kk_KZ",
			"ko_KR",
			"lt_LT",
			"lv_LV",
			"me_ME",
			"mn_MN",
			"ms_MY",
			"nb_NO",
			"ne_NP",
			"nl_BE",
			"nl_NL",
			"pl_PL",
			"pt_BR",
			"pt_PT",
			"ro_MD",
			"ro_RO",
			"ru_RU",
			"sk_SK",
			"sl_SI",
			"sr_Cyrl_RS",
			"sr_Latn_RS",
			"sr_RS",
			"sv_SE",
			"th_TH",
			"tr_TR",
			"uk_UA",
			"vi_VN",
			"zh_CN",
			"zh_TW",
		];
	}
}