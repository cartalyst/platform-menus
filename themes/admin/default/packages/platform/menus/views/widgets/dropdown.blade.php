<select{{ $attributes }}>
@foreach ($customOptions as $id => $value)
<option value="{{ $id }}">{{ $value }}</option>
@endforeach
@each('platform/menus::widgets/dropdown/item', $items, 'item')
</select>
