# Menus Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/platform-menus/labels/Accepted)
- [Rejected](https://github.com/cartalyst/platform-menus/labels/Rejected)

---

### v3.1.1 - 2015-08-24

`REMOVED`

- Usage of Illuminate\Html package.

`UPDATED`

- Minor consistency tweaks.

`FIXED`

- Wrong language key used on the popover helper.

### v3.1.0 - 2015-07-24

`REVISED`

- Use `fillable` instead of `guarded` on the model.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v3.0.2 - 2015-09-14

`ADDED`

- Missing permissions.

### v3.0.1 - 2015-08-24

`REMOVED`

- Usage of Illuminate\Html package.

`UPDATED`

- Minor consistency tweaks.

`FIXED`

- Wrong language key used on the popover helper.

### v3.0.0 - 2015-07-07

- Updated for Platform 4.

### v2.2.1 - 2015-08-24

`UPDATED`

- Minor consistency tweaks.

`FIXED`

- Wrong language key used on the popover helper.

### v2.2.0 - 2015-07-20

`REVISED`

- Use `fillable` instead of `guarded` on the model.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v2.1.3 - 2015-08-24

`UPDATED`

- Minor consistency tweaks.

`FIXED`

- Wrong language key used on the popover helper.

### v2.1.2 - 2015-06-30

`UPDATES`

- Consistency tweaks.

### v2.1.1 - 2015-06-13

`FIXED`

- Bulk delete selector listener.

### v2.1.0 - 2015-05-30

`ADDED`

- Check for page permissions when displaying menus.

`FIXED`

- Optimized menu display on menu manager.
- Various fixes applied on the jQuery Menu Mananger plugin.
- Menu children from being shown when the root menu was disabled.
- Incorrect target option having the wrong values on the markup.

### v2.0.1 - 2015-04-10

`FIXED`

- A bug that prevented menu items from expanding again after hitting update.

### v2.0.0 - 2015-03-05

- Updated for Platform 3.

### v1.2.1 - 2015-08-24

`UPDATED`

- Minor consistency tweaks.

`FIXED`

- Wrong language key used on the popover helper.

### v1.2.0 - 2015-07-16

`REVISED`

- Use `fillable` instead of `guarded` on the model.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v1.1.3 - 2015-08-24

`UPDATED`

- Minor consistency tweaks.

`FIXED`

- Wrong language key used on the popover helper.

### v1.1.2 - 2015-06-30

`UPDATED`

- Consistency tweaks.

### v1.1.1 - 2015-06-13

`FIXED`

- Bulk delete selector listener.

### v1.1.0 - 2015-05-30

`ADDED`

- Check for page permissions when displaying menus.

`FIXED`

- Optimized menu display on menu manager.
- Various fixes applied on the jQuery Menu Mananger plugin.
- Menu children from being shown when the root menu was disabled.
- Incorrect target option having the wrong values on the markup.

### v1.0.3 - 2015-04-10

`FIXED`

- A bug that prevented menu items from expanding again after hitting update.

### v1.0.2 - 2015-02-18

`FIXED`

- A bug causing the form to be submitted when pressing enter.

### v1.0.1 - 2015-01-26

`FIXED`

- Fixed an issue on the latest underscore.js version.

### v1.0.0 - 2015-01-23

- Can create, update, delete menus.
- Can assign menu name & slug.
- Can enabled/disable.
- Can create, update, delete menu links.
- Can sort menu links using nested sets model.
- Can set link name & slug.
- Can set link type. static | page
- Can set URI.
- Can enable/disable link.
- Can set link target. same window | new window | parent window | top frame
- Can set https. inherit | yes | no
- Can select link parent.
- Can set link class.
- Can set link Regular Expression.
- Can set Visibility. Show Always | Logged in | Logged out | Admin Only
- Can restrict to roles.
- Has blade call `@nav('slug', 'depth', 'cssClass', 'beforeUri', 'view')`
- Has blade call `@dropdown('slug', 'depth', 'selected')`.
