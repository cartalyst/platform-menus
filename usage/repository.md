### Repository

#### IoC Binding

The menu repository is bound to `platform.menus` and can be resolved out of the IoC Container using that offset.

```php
$menus = app('platform.menus');
```

#### Methods

The repository contains several methods that are used throughout the extension, most common methods are listed below.

For an exhaustive list of available methods, checkout the `MenuRepositoryInterface`

- findAll();

Returns a collection of all menus.

- findAllRoot();

Returns a collection of all root menus.

- find($id);

Returns a menu object based on the given id.

- findBySlug($slug);

Returns a menu object based on the given slug.

- create(array $data);

Creates a new menu.

- update($id, array $data);

Updates an existing menu.

- delete($id);

Deletes a menu.

- enable($id);

Enables a menu.

- disable($id);

Disables a menu.
