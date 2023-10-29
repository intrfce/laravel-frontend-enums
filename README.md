# Laravel Frontend Enums

[![Latest Version on Packagist](https://img.shields.io/packagist/v/intrfce/laravel-frontend-enums.svg?style=flat-square)](https://packagist.org/packages/intrfce/laravel-frontend-enums)
[![Total Downloads](https://img.shields.io/packagist/dt/intrfce/laravel-frontend-enums.svg?style=flat-square)](https://packagist.org/packages/intrfce/laravel-frontend-enums)
![GitHub Actions](https://github.com/intrfce/laravel-frontend-enums/actions/workflows/main.yml/badge.svg)

Publish your PHP enums to the frontend of our application so you can refer to them in your JavaScript code.

## Installation

You can install the package via composer:

```bash
composer require intrfce/laravel-frontend-enums
```

## Usage

In your `AppServiceProvider.php`, tell the package which Enums you want to publish:

```php

use Intrfce\LaravelFrontendEnums\Facades\PublishEnums;

PublishEnums::publish([
    \App\Enums\MyEnum::class,
    \App\Enums\MyOtherEnum::class,
])->toDirectory(resource_path('js/Enums'));
```
Then run the publish command:

```php
php artisan publish:enums-to-javascript
```

Your enums will be waiting at the path you specified with the extension `.enum.js`:

```
MyEnum.enum.js
MyOtherEnum.enum.js
```

You can then import and use them in your JavaScript code:

```js  
import {MyEnum} from './Enums/MyEnum.enum.js';
import {MyOtherEnum} from './Enums/MyOtherEnum.enum.js';

console.log(MyEnum.FOO); // 0
console.log(MyOtherEnum.BAR); // 'bar'
```

## Typescript Support

Typescript support is baked in: just add `->asTypescript()` to the list of enums in your `AppServiceProvider.php`:

```php
PublishEnums::publish([
    \App\Enums\MyEnum::class,
    \App\Enums\MyOtherEnum::class,
])
->asTypescript()
->toDirectory(resource_path('js/Enums'));
```

Files will be output as `.ts` files and Typescript native enums:

```ts
export enum MyEnum {
    FOO = 0,
    BAR = 1,
    BAZ = 2,
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