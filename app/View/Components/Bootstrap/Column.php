<?php

namespace App\View\Components\Bootstrap;

use App\Enums\Bootstrap\Breakpoint;
use Illuminate\Support\Str;
use Illuminate\View\Component;


class Column extends Component {
	/**
	 * @var bool
	 */
	public bool $gutters = true;

	public string $breakpoint = '';

	public function __construct(string $breakpoint = 'EXTRA_SMALL', ?int $width = null, bool $gutters = true) {
		$this->gutters = $gutters;
		$breakpoints = explode(';', $breakpoint);
		$breakpoints = collect($breakpoints)->map(function($item, $key) use($width) {
			list($breakpoint, $wdh) = explode('|', $item .= Str::endsWith($item, '|') ? null : '|');
			$breakpoint = strtoupper($breakpoint);
			$wdh = $wdh ?: $width;
			return Breakpoint::hasKey($breakpoint) ? Breakpoint::$breakpoint($wdh)->col : 'col';
		});
		$this->breakpoint = trim($breakpoints->join(' '));
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render() {
		return view('components.bootstrap.column');
	}
}
