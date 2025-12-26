<?php

namespace App\Http\Livewire;

use Livewire\Component;


class Counter extends Component {
	public $count = 0;

	protected $listeners = ['postAdded' => 'increment'];

	public function increment() {
		$this->count++;
	}

	public function render() {
		return view('livewire.counter');
	}
}
