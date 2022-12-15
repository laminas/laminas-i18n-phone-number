# PhoneNumber

`Laminas\I18n\PhoneNumber\Validator\PhoneNumber` allows you to determine if a given value **is a valid phone number**.
Phone number formats are country specific.

## Basic Usage

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber();
$validator->isValid('+4930123456'); // true

When the validator receives a phone number in a recognizable international format, including the leading country dialing code, it will determine validity based on rules specific to the corresponding country.

Validation is delegated to a dedicated library [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) which itself is a port of [Google's libphonenumber](https://github.com/google/libphonenumber).

## Accept more Lenient Data by Providing a Country Code

By providing a country code to the validator, you can validate numbers that lack the leading country dialing code.

```php
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;

$validator = new PhoneNumber([
    'country' => 'GB',
]);

$validator->isValid('01234 567 890'); // true - a valid national UK number
$validator->isValid('+1 (510) 123-4567'); // true - a US number including a dialing code
$validator->isValid('(510) 123-4567'); // false - a national US number that is invalid for 'GB'
```

TIP: **Default Country Detected by System Locale**
When the validator is used as part of a Mezzio or Laminas application, the validator is created by a factory that automatically populates the default country code from a configuration item, falling back to the system locale.

## Use Additional Validation Context to Validate Phone Numbers for any Country

If your application needs to validate phone numbers from anywhere around the globe, you will need to provide additional context to the validator. Laminas validators accept a second, array parameter representing an entire payload of data. This payload will be searched for country code if you provide the name of the expected field as an option to the validator:

```php
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;

$validator = new PhoneNumber([
    'country' => 'ZA',
    'country_context' => 'customer-country-field-name',
]);

$postPayload = [
    'number' => '01234 567 890',
    'customer-country-field-name' => 'GB',
    'other-data' => '…',
];

$validator->isValid($postPayload['number'], $postPayload); // true
```

## Limiting Acceptable Number Types

The validator is capable of validating a range of number types; mobile numbers, fixed lines, premium rate and emergency numbers to name a few.
By default, the validator will accept any number type:

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber([
    'country' => 'US',
]);
$validator->isValid('911'); // true

In order to reduce the range of acceptable number types, provide the validator with a bitmask of types from `PhoneNumberValue` value object:

```php
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;

$allowed = PhoneNumberValue::TYPE_FIXED | PhoneNumberValue::TYPE_VOIP;

$validator = new PhoneNumber([
    'allowed_types' => $allowed,
]);

$validator->isValid('+44 (0) 1234 567 890'); // true. GB Fixed Line
$validator->isValid('+44 (0) 7723 123 456'); // false. GB Mobile
```

The possible types are as follows:

- `PhoneNumberValue::TYPE_FIXED`
- `PhoneNumberValue::TYPE_MOBILE`
- `PhoneNumberValue::TYPE_TOLL_FREE`
- `PhoneNumberValue::TYPE_PREMIUM_RATE`
- `PhoneNumberValue::TYPE_SHARED_COST`
- `PhoneNumberValue::TYPE_VOIP`
- `PhoneNumberValue::TYPE_PERSONAL`
- `PhoneNumberValue::TYPE_PAGER`
- `PhoneNumberValue::TYPE_UAN`
- `PhoneNumberValue::TYPE_EMERGENCY`
- `PhoneNumberValue::TYPE_VOICEMAIL`
- `PhoneNumberValue::TYPE_SHORT_CODE`
- `PhoneNumberValue::TYPE_STANDARD_RATE`
- `PhoneNumberValue::TYPE_UNKNOWN`

And there are some additional convenience constants for common use-cases:

- `PhoneNumberValue::TYPE_KNOWN` - All types except `TYPE_UNKNOWN`
- `PhoneNumberValue::TYPE_ANY` - Any type of valid number
- `PhoneNumberValue::TYPE_RECOMMENDED` - Fixed lines, mobiles and Voip numbers

## Summary of Constructor Options

```php
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;

$options = [
    'country' => 'ZA', // Can also be a locale string such as 'fr_FR'
    'country_context' => 'your-country-field-name',
    'allowed_types' => PhoneNumberValue::TYPE_MOBILE,
];

$validator = new PhoneNumber($options);
```

Options can also be changed at runtime with:

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber();
$validator->setOptions($options);
```

## Runtime Modification of Options

Each of the 3 options have companion "setters" to change the runtime behaviour of the validator after it has been constructed:

### Set Country Code

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber();
$validator->setCountry('US');
```

A locale string can also be used to set the country.

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber();
$validator->setCountry('de_DE');
```

### Set the Country Validation Context Key

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber();
$validator->setCountryContext('my-country-input');
```

### Set the Acceptable Number Types

```php
$validator = new Laminas\I18n\PhoneNumber\Validator\PhoneNumber();
$validator->setAllowedTypes(
    Laminas\I18n\PhoneNumber\PhoneNumberValue::TYPE_VOIP
);
```
