# Filament Phone Input

<p align="center"><img src="/screenshots/input.png" alt="Filament Phone Input"></p>

<p align="center">
<a href="https://packagist.org/packages/ysfkaya/filament-phone-input" rel="nofollow"><img src="https://camo.githubusercontent.com/62003a16457f1d78e40daffa586f4cba87f7ce85ae515f17f9d4c5bb040d0c84/68747470733a2f2f696d672e736869656c64732e696f2f7061636b61676973742f762f7973666b6179612f66696c616d656e742d70686f6e652d696e7075743f636f6c6f723d72676228353625323031383925323032343829266c6162656c3d72656c65617365267374796c653d666f722d7468652d6261646765" alt="Latest Version on Packagist" data-canonical-src="https://img.shields.io/packagist/v/ysfkaya/filament-phone-input?color=rgb(56%20189%20248)&amp;label=release&amp;style=for-the-badge" style="max-width: 100%;"></a>
<a href="https://github.com/ysfkaya/filament-phone-input/actions?query=workflow%3Arun-tests+branch%3Amain"><img src="https://camo.githubusercontent.com/90ac79a0bd403864d0ba672706bafb7d23e6253cc15991e8ba98e6db05e7a3d8/68747470733a2f2f696d672e736869656c64732e696f2f6769746875622f616374696f6e732f776f726b666c6f772f7374617475732f7973666b6179612f66696c616d656e742d70686f6e652d696e7075742f72756e2d74657374732e796d6c3f6272616e63683d6d61696e266c6162656c3d7465737473267374796c653d666f722d7468652d6261646765" alt="GitHub Tests Action Status" data-canonical-src="https://img.shields.io/github/actions/workflow/status/ysfkaya/filament-phone-input/run-tests.yml?branch=main&amp;label=tests&amp;style=for-the-badge" style="max-width: 100%;"></a>
<a href="https://packagist.org/packages/ysfkaya/filament-phone-input" rel="nofollow"><img src="https://camo.githubusercontent.com/1e5c73106f787d6f24d6a16423df9d786f9c775ceea0f5ec4c35776c06ed52ae/68747470733a2f2f696d672e736869656c64732e696f2f7061636b61676973742f64742f7973666b6179612f66696c616d656e742d70686f6e652d696e7075742e7376673f636f6c6f723d7267622532383234392532303131352532303232253239267374796c653d666f722d7468652d6261646765" alt="Total Downloads" data-canonical-src="https://img.shields.io/packagist/dt/ysfkaya/filament-phone-input.svg?color=rgb%28249%20115%2022%29&amp;style=for-the-badge" style="max-width: 100%;"></a>
<a target="_blank" rel="noopener noreferrer nofollow" href="https://camo.githubusercontent.com/0a994a237658310190d4990f0eb27e59934b82cd8858a43ed39aeb6c36c1c930/68747470733a2f2f696d672e736869656c64732e696f2f7061636b61676973742f646570656e64656e63792d762f7973666b6179612f66696c616d656e742d70686f6e652d696e7075742f7068703f636f6c6f723d726762253238313635253230313830253230323532253239266c6f676f3d706870266c6f676f436f6c6f723d726762253238313635253230313830253230323532253239267374796c653d666f722d7468652d6261646765"><img src="https://camo.githubusercontent.com/0a994a237658310190d4990f0eb27e59934b82cd8858a43ed39aeb6c36c1c930/68747470733a2f2f696d672e736869656c64732e696f2f7061636b61676973742f646570656e64656e63792d762f7973666b6179612f66696c616d656e742d70686f6e652d696e7075742f7068703f636f6c6f723d726762253238313635253230313830253230323532253239266c6f676f3d706870266c6f676f436f6c6f723d726762253238313635253230313830253230323532253239267374796c653d666f722d7468652d6261646765" alt="Packagist PHP Version" data-canonical-src="https://img.shields.io/packagist/dependency-v/ysfkaya/filament-phone-input/php?color=rgb%28165%20180%20252%29&amp;logo=php&amp;logoColor=rgb%28165%20180%20252%29&amp;style=for-the-badge" style="max-width: 100%;"></a>
<a target="_blank" rel="noopener noreferrer nofollow" href="https://camo.githubusercontent.com/a571aa4ecfbe895b6edb4a58a79a859f2060b7ea30cda5f0450002849bda0bf3/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f66696c616d656e742d332d7267622832333525323036382532303530293f7374796c653d666f722d7468652d6261646765266c6f676f3d6c61726176656c"><img src="https://camo.githubusercontent.com/a571aa4ecfbe895b6edb4a58a79a859f2060b7ea30cda5f0450002849bda0bf3/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f66696c616d656e742d332d7267622832333525323036382532303530293f7374796c653d666f722d7468652d6261646765266c6f676f3d6c61726176656c" alt="Filament Version" data-canonical-src="https://img.shields.io/badge/filament-3-rgb(235%2068%2050)?style=for-the-badge&amp;logo=laravel" style="max-width: 100%;"></a>
</p>

## Table of Contents

-   [Introduction](#introduction)
-   [Installation](#installation)
-   [Upgrade From 1.x](#upgrade-from-1x)
-   [Usage](#usage)
    -   [Seperate Country Code](#seperate-country-code)
    -   [Validation](#validation)
    -   [Display Number Format](#display-number-format)
    -   [Input Number Format](#input-number-format)
    -   [Focus Input Type](#focus-input-type)
    -   [Disallow Dropdown](#disallow-dropdown)
    -   [Custom Container](#custom-container)
    -   [Exclude Countries](#exclude-countries)
    -   [Initial Country](#initial-country)
    -   [Only Countries](#only-countries)
    -   [Format On Display](#format-on-display)
    -   [Geo Ip Lookup](#geo-ip-lookup)
    -   [Placeholder Number Type](#placeholder-number-type)
    -   [Separate Dial Code](#separate-dial-code)
    -   [Outside Filament](#outside-filament)
    -   [More](#more)
-   [Testing](#testing)
-   [Changelog](#changelog)
-   [Credits](#credits)
-   [License](#license)

### Introduction

This package provides a phone input component for [Laravel Filament](https://filamentphp.com/). It uses [International Telephone Input](https://github.com/jackocnr/intl-tel-input) to provide a dropdown of countries and flags.

This package also includes with [Laravel Phone](https://github.com/propaganistas/laravel-phone) package. You can use all the methods of the Laravel Phone package.

> **Note** 
> For **Filament 2.x** use **[1.x](https://github.com/ysfkaya/filament-phone-input/tree/1.x)** branch

## Installation

You can install the package via composer:

```bash
composer require ysfkaya/filament-phone-input
```

## Upgrade From 1.x

If you are upgrading from 1.x, you should publish the assets again.

```bash
php artisan filament:assets
```

#### Namespace

```diff
- use Ysfkaya\FilamentPhoneInput\PhoneInput;
+ use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
```

#### Deprecates

<!-- Diff -->    
```diff
- protected ?string $customPlaceholder = null;
- public function customPlaceholder(?string $value)
```

## Usage

```php
use Filament\Forms;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneInputColumn;

  //...
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true),

                PhoneInput::make('phone'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                PhoneInputColumn::make('phone')
            ]);
    }
```

#### Seperate Country Code
----
Sometimes you may want to save the country code and the phone number in different columns. You can use the `countryStatePath` method to do that.

```php
PhoneInput::make('phone')
    ->countryStatePath('phone_country')
```

Table column:

```php
PhoneInputColumn::make('phone')
    ->countryColumn('phone_country')
```

When you use the `countryStatePath` method, the country code will be saved to the `phone_country` column and the phone number will be saved to the `phone` column.

#### Validation
----

You may validate the phone number by using the `validateFor` method:

```php
PhoneInput::make('phone')
    ->validateFor(
        country: 'TR' | ['US', 'GB'], // default: 'AUTO'
        type: libPhoneNumberType::MOBILE | libPhoneNumberType::FIXED_LINE, // default: null
        lenient: true, // default: false
    ),
```

> **Warning** 
> Add an extra translation to your `validation.php` file.

You can find more information about the validation [here](https://github.com/Propaganistas/Laravel-Phone#validation)

#### Display Number Format
----

You may set the display format of the phone number by passing a format string to the `displayNumberFormat` method. The default format is `NATIONAL`. That means the phone number will be displayed in the format of the selected country.

> Available formats are; 

- PhoneInputNumberType::E164
- PhoneInputNumberType::INTERNATIONAL
- PhoneInputNumberType::NATIONAL
- PhoneInputNumberType::RFC3966

```php
PhoneInput::make('phone')
    ->displayNumberFormat(PhoneInputNumberType::E164),
```

<p align="left"><img src="/screenshots/display-number-format.png" alt="Filament Phone Input"></p>

#### Input Number Format
----

You may set the input value type by passing a type string to the `inputNumberFormat` method. The default type is `E164`. That means the phone number will be saved in the format of the selected country to the **database**.

```php
PhoneInput::make('phone')
    ->inputNumberFormat(PhoneInputNumberType::NATIONAL),
```

#### Focus Input Type
----

You may set the focus input type by passing a type string to the `focusNumberFormat` method. The default value is `false`.

```php
PhoneInput::make('phone')
    ->focusNumberFormat(PhoneInputNumberType::E164),
```

<p align="left"><img src="/screenshots/focus-input-type.gif" alt="Filament Phone Input"></p>

#### Disallow Dropdown
----

You may disable the dropdown by using the `disallowDropdown` method:

```php
PhoneInput::make('phone')
    ->disallowDropdown(),
```

<p align="left"><img src="/screenshots/disallowed-dropdown.png" alt="Filament Phone Input"></p>

#### Auto Placeholder
----

You may set the auto placeholder type by using the `autoPlaceholder` method:

```php
PhoneInput::make('phone')
    ->autoPlaceholder('polite'), // default is 'aggressive'
```

#### Custom Container
----

You may set the additional classes to add to the parent div by using the `customContainer` method:

```php
PhoneInput::make('phone')
    ->customContainer('w-full'),
```

#### Exclude Countries
----

You may set the exclude countries by using the `excludeCountries` method:

```php
PhoneInput::make('phone')
    ->excludeCountries(['us', 'gb']),
```

#### Initial Country
----

You may set the initial country by using the `initialCountry` method:

```php
PhoneInput::make('phone')
    ->initialCountry('us'),
```

#### Only Countries
----

You may set the only countries by using the `onlyCountries` method:

```php
PhoneInput::make('phone')
    ->onlyCountries(['tr','us', 'gb']),
```

<p align="left"><img src="/screenshots/only-countries.png" alt="Filament Phone Input"></p>

#### Format On Display

You may set the format on display by using the `formatOnDisplay` method:

```php
PhoneInput::make('phone')
    ->formatOnDisplay(false),
```

#### Geo Ip Lookup
----

In default, the package performs a geoIp lookup to set the initial country while mounting the component. To disable this feature, you may use the `disableIpLookUp` method:

```php
PhoneInput::make('phone')
    ->disableIpLookUp(),
```

You may set the geoIp lookup by using the `geoIpLookup` method:

```php
PhoneInput::make('phone')
    ->ipLookup(function () {
        return rescue(fn () => Http::get('https://ipinfo.io/json')->json('country'), app()->getLocale(), report: false);
    })
```

#### Placeholder Number Type
----

You may set the placeholder number type by using the `placeholderNumberType` method:

```php
PhoneInput::make('phone')
    ->placeholderNumberType('FIXED_LINE'),
```

#### Preferred Countries
----

You may set the preferred countries by using the `preferredCountries` method:

```php
PhoneInput::make('phone')
    ->preferredCountries(['tr','us', 'gb']),
```

#### Separate Dial Code
----

You may set the separate dial code by using the `separateDialCode` method:

```php
PhoneInput::make('phone')
    ->separateDialCode(true),
```

#### Outside Filament

A livewire component:

```php
<?php

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\View;
use Livewire\Component as Livewire;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Component extends Livewire implements HasForms
{
    use InteractsWithForms;

    public $data;

    public function mount()
    {
        // Do not forget to fill the form
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                PhoneInput::make('phone'),
            ])->statePath('data');
    }

    public function render()
    {
        return view('livewire.component');
    }
}
```

A blade component:

```blade
{{-- views/livewire/component.blade.php --}}
<div>
    {{ $this->form }}
</div>
```

A blade layout:

```blade
{{-- views/components/layouts/app.blade.php --}}
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script
        src="https://cdn.jsdelivr.net/npm/async-alpine@1.x.x/dist/async-alpine.script.js"
        defer
    ></script>
    <script
        src="https://unpkg.com/alpine-lazy-load-assets@latest/dist/alpine-lazy-load-assets.cdn.js"
        defer
    ></script>
</head>
<body>
    {{ $slot }}

    @stack('scripts')
</body>
</html>
```

#### More
----

You can find the more documentation for the intel tel input [here](https://intl-tel-input.com/)


<a name="testing"></a>

## Testing

Run following command to prepare testing environment.

```bash
composer prepare-test
```

Run following command to run tests.

```bash
composer test
```

<a name="changelog"></a>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

<a name="credits"></a>

## Credits

-   [Yusuf Kaya](https://github.com/ysfkaya)
-   [All Contributors](../../contributors)

<a name="license"></a>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
