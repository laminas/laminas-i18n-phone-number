# ToNationalPhoneNumber

The `ToNationalPhoneNumber` filter can be used to format phone numbers to the national format expected by the region the phone number belongs to.  

## Basic Usage

```php
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;

$country = CountryCode::fromString('US');
$filter  = new ToNationalPhoneNumber($country);

echo $filter->filter('+1 510 123 4567'); // "(510) 123-4567"
```

Regardless of the configured default country code, the `ToNationalPhoneNumber` filter will still format a recognisable number for any region:

```php
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;

$country = CountryCode::fromString('SE');
$filter  = new ToNationalPhoneNumber($country);

echo $filter->filter('+441234567890'); // "01234 567890"
```

## Changing the default country code

Given an already instantiated filter instance, the country code can be changed in 2 ways:

- Via the `setCountryCode` method
- Via the `setOptions` method

The `setCountryCode` method accepts a `CountryCode` value object, or a 2-letter [ISO 3166 country code](https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes) string:

```php
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;

$country = CountryCode::fromString('GB');
$filter  = new ToNationalPhoneNumber($country);

$filter->setCountryCode('FR');
// …or
$filter->setCountryCode(CountryCode::fromString('DE'));
```

The `setOptions` method accepts an array or iterable in the following format:

```php
$options = [
    'country-code' => 'US',
];

$filter->setOptions($options);
```

## Filtering invalid data

When the filter receives values that cannot be recognised as valid phone numbers, the original input is returned as-is.

```php
$filter->filter('Not a phone number'); // "Not a phone number"
```
