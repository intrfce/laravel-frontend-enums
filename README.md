# Laravel Frontend Enums

[![Latest Version on Packagist](https://img.shields.io/packagist/v/intrfce/laravel-frontend-enums.svg?style=flat-square)](https://packagist.org/packages/intrfce/laravel-frontend-enums)
[![Total Downloads](https://img.shields.io/packagist/dt/intrfce/laravel-frontend-enums.svg?style=flat-square)](https://packagist.org/packages/intrfce/laravel-frontend-enums)
![GitHub Actions](https://github.com/intrfce/laravel-frontend-enums/actions/workflows/main.yml/badge.svg)

Publish your PHP enums to the front-end of your application, so you can refer to them in your JavaScript code.

This means less reliance on "magic strings".

```js

// Before:

if (myValue === 'name') {
  // Do something
}

// After
import {UserProfileField} from './UserProfileField.enum.js';

if (myValue === UserProfileField.Name) {
  // Do something.
}
````

## Installation

You can install the package via composer:

```bash
composer require intrfce/laravel-frontend-enums
```

## Usage


### Just add the attribute!

Add the `#[PublishEnum]` attribute to any enum you want published to the frontend - it's that easy.

```php
use Intrfce\LaravelFrontendEnums\Attributes\PublishEnum;

#[PublishEnum]
enum MyEnum: string {
    case Foo = 'foo';
    case Bar = 'bar';
}
```

Then run the publish command:

```bash
php artisan publish:enums-to-javascript
```

Your enums will be waiting at `resources/js/Enums` with the extension `.enum.js`:

```js
import {MyEnum} from './Enums/MyEnum.enum.js';

console.log(MyEnum.Foo); // 'foo'
```

### Configuration

Publish the config file to customise the output path and discovery directories:

```bash
php artisan vendor:publish --tag=config --provider="Intrfce\LaravelFrontendEnums\LaravelFrontendEnumsServiceProvider"
```

```php
// config/laravel-frontend-enums.php
return [
    
    // Customise the output directory.
    'publish_to' => resource_path('js/Enums'),
    
    // Customise the folders scanned for enum classes
    'discover_in' => [
        app_path(),
    ],
    
    // Always output as typescript enums.
    'as_typescript' => true,
    
];
```

The `discover_in` array supports glob patterns, which is useful for modular or monorepo layouts:

```php
'discover_in' => [
    app_path(),
    base_path('app-modules/*/src'),
],
```

## Typescript Support

Enable TypeScript output globally via the config file:

```php
'as_typescript' => true,
```

Or override per-enum using the attribute:

```php
#[PublishEnum(asTypescript: true)]  // Force TypeScript for this enum
#[PublishEnum(asTypescript: false)] // Force JavaScript for this enum
#[PublishEnum]                      // Follow the global config setting
```

TypeScript enums are output as `.ts` files:

```ts
export enum MyEnum {
    Foo = "foo",
    Bar = "bar",
}
```

## Automatically generate javascript files on change.

You can use the [`vite-plugin-watch`](https://github.com/lepikhinb/vite-plugin-watch) package from [lepikhinb](https://github.com/lepikhinb) to automatically generate your javascript files when you make changes to your PHP enums:

```php
npm install -D vite-plugin-watch
```

Then add the plugin to your `vite.config.js`:

```js
import { defineConfig } from "vite"
import { watch } from "vite-plugin-watch"

export default defineConfig({
  plugins: [ 
    watch({
      pattern: "app/Enums/**/*.php",
      command: "php artisan publish:enums-to-javascript",
    }),
  ],
})
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email dan@danmatthews.me instead of using the issue tracker.

## Credits

-   [Dan Matthews](https://github.com/intrfce)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
