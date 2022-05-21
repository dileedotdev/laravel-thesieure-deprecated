# Provide a helper interface to communicate with thesieure easily

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dinhdjj/thesieure.svg?style=flat-square)](https://packagist.org/packages/dinhdjj/thesieure)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dinhdjj/thesieure/run-tests?label=tests)](https://github.com/dinhdjj/thesieure/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dinhdjj/thesieure/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dinhdjj/thesieure/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dinhdjj/thesieure.svg?style=flat-square)](https://packagist.org/packages/dinhdjj/thesieure)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require dinhdjj/laravel-thesieure
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="thesieure-config"
```

This is the contents of the published config file:

```php
// config for dinhdjj/laravel-thesieure
return [
    'domain' => env('THESIEURE_DOMAIN', 'thesieure.com'),
    'partner_id' => env('THESIEURE_PARTNER_ID'),
    'partner_key' => env('THESIEURE_PARTNER_KEY'),

    /**
     * The callback will be call when thesieure callback to server.
     */
    'callback' => [
        'route' => [
            'name' => 'thesieure.callback',
            'uri' => 'api/thesieure/callback',
            'middleware' => [
                'api',
            ],
            'method' => 'post',
        ],
    ],

    /**
     * Used when fetch card types from thesieure server.
     */
    'fetch_card_types' => [
        'cache' => [
            'enabled' => true,
            'key' => 'thesieure.card_types',
            'ttl' => 60 * 5, // 5 minutes,
            'store' => null, // used default store
        ],
    ],
];
```

## Usage

Firstly, you should register logic to handle the callback from thesieure.

```php
// app/Providers/AppServiceProvider.php

public function boot()
{
    \Thesieure::onCallback(function(Dinhdjj\Thesieure\Types\ApprovedCard $card){
        // Each property of $card equivalent to thesieure's attributes read more on `thesieure`
        // Besides, $card also have some helper methods

        if($card->isApproving()){
            // Do something
        }

        if($card->isSuccess()){
            $receivedValue = $card->getReceivedValue();
            $realFaceValue = $card->getRealFaceValue();

            // Do something
        }

        if($card->isError()){
            // Do something
        }
    });
}
```

Next, you should register the callback in thesieure. By default, the callback route is`post`:`/api/thesieure/callback`.

Finally, when you need to send a card to `thesieure` approve:

```php
    \Thesieure::approveCard('VIETTEL', 20000, '20002346728333', '239847923483242432', 'anything');
    \Thesieure::updateApprovedCard('VIETTEL', 20000, '20002346728333', '239847923483242432', 'anything');
```

When `thesieure` response the result, error or when you call `approveCard`, `updateApprovedCard` method, closure you transfer to `\Thesieure::onCallback` will be invoked. All things you need to do is register logic in `\Thesieure::onCallback`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [dinhdjj](https://github.com/dinhdjj)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
