<select{{ $attributes }}>
@foreach ($customOptions as $id => $value)
<option id="{{ $id }}">{{ $value }}</option>
@endforeach
@each('platform/menus::widgets/dropdown/child', $children, 'child')
</select>
