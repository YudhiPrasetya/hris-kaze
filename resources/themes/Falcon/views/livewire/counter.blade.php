<div style="text-align:center;">
	<button class="btn-falcon-primary btn mr-1" wire:click="$emitTo('show-posts', 'postAdded')">Add Counter</button>
	<button wire:click="increment">+</button>
	<h1>{{ $count }}</h1>
</div>