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

### Testing

```bash
composer test
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