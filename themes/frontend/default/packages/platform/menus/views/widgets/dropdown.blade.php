<select{{ $attributes }}>
@foreach ($customOptions as $id => $value)
<option value="{{ $id }}">{{ $value }}</option>
@endforeach
@each('platform/menus::widgets/dropdown/child', $items, 'item')
</select>
