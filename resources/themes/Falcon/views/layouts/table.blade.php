@php
	$id = md5(rand(0, 1000));
	if(isset($hasToolbar) && $hasToolbar == true) {
		if (isset($data)) {
			$data['toolbar'] = "#toolbar-$id";
			// $data['search-selector'] = ".table-search-input-$id";
		}
	}
@endphp
@if(isset($hasToolbar) && $hasToolbar === true)
<div id="toolbar-{{ $id }}">
	<div class="form-inline" role="form">
		@if(isset($customContent)){!! $customContent !!}@endif
		@if(isset($canAdd) && $canAdd === true)
		<div class="form-group mb-0">
			<a href="{{ $addUrl ?? '#' }}" role="button" class="btn btn-falcon-primary mr-3">
				<i class="{{ $addIcon ?? 'fas fa-file-plus' }}"></i>
				<span class="d-none d-sm-inline-block ml-1">{{ $addText ?? 'New' }}</span>
			</a>
		</div>
		@endif
	</div>
</div>
@endif
<table
	class="bootstrap-table-custom table-striped"
	@isset($data)
		@foreach($data as $key => $value)
			data-{{ $key }}="{!! $value !!}"
		@endforeach
	@endisset
	>
	<thead class="thead-light">
	<tr>
		<th scope="col" data-class="text-center va-baseline font-weight-medium" data-field="no" data-sortable="false" data-width="75">#</th>
		@isset($columns)
			@foreach($columns as $column)
				@php
					$attribs = [];
					foreach ($column['attrs'] as $key => $value) {
						$attribs[] = "data-$key=\"$value\"";
					}
				@endphp
				<th scope="col" {!! implode(' ', $attribs) !!}>{!! $column['title'] !!}</th>
			@endforeach
		@endisset
		@if(isset($hasActions) && $hasActions === true)
			<th scope="col" data-class="text-center va-baseline text-nowrap bootstrap-table-actions" data-field="actions" data-sortable="false" data-formatter="actionsFormatter" data-width="154">Actions</th>
		@endif
	</tr>
	</thead>
</table>
