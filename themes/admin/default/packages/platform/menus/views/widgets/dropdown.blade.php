<select{{ $attributes }}>
	@each('platform/menus::widgets/dropdown/child', $children, 'child')
</select>
