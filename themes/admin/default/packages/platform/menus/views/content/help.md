The menus extension allows you to create and manage menus across your application.
Features include conditional visibilities based on roles.

---

### Widgets

`@widget('platform/menus::nav.show', [ 'slug', 'depth', 'cssClass', 'beforeUri', 'view' ])`

This blade call will allow you to output the selected menu on your views.

	// Outputs the platform `admin` menu
	@widget('platform/menus::nav.show', [ 'admin', 0, 'menu menu--sidebar', admin_uri(), 'partials/navigation/sidebar' ])

	// Outputs the `foo` menu
	@widget('platform/menus::nav.show', [ 'foo' ])

---

### When should I use it?

Whenever you need to manage or create menus for your application.

---

### How can I use it?

1. Create a Menu.
2. Fill out name, slug.
3. Create a new link.
	- Fill out the details.
	- Fill out advanced settings. (if needed)
	- Click add to add the item to your menu.
4. Hit save.

That's it, once you have created your menu, you can reference it on your view using the widget.
