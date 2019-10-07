### Examples

The `$menus` variable used below is a reference to the MenuRepository.

```php
$menus = app('platform.menus');
```

###### Retrieve all menus

```php
$allMenus = $menus->findAll();
```

###### Dynamically create a new menu.

```php
$menus->create([
    'name' => 'Foo',
    'slug' => 'foo',
]);
```
