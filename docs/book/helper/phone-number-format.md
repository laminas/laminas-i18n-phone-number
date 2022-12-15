# PhoneNumberFormat

The `Laminas\I18n\PhoneNumber\View\Helper\PhoneNumberFormat` view helper provides a convenient way of formatting phone numbers in projects that make use of [`laminas/laminas-view`](https://docs.laminas.dev/laminas-view/).

--8<-- "installation-requirements.md"

## Basic Usage

The helper is aliased to `phoneNumberFormat` so within view scripts, it can be invoked in the following way:

```php
<?php
$phoneNumber = '01234 567 890';
?>

<p>My phone number is <?= $this->phoneNumberFormat()->toInternational($phoneNumber, 'GB') ?>.</p>
```

The resulting markup here would be:

```html
<p>My phone number is +44 1234 567890.</p>
```

## The Configured Default Country and the Country Code Parameter

In a Mezzio or Laminas MVC application, you can configure the "default" country for phone numbers in your application config by specifying

```php
return [
    'laminas-i18n-phone-number' => [
        'default-country-code' => 'US',
    ],
];
```

When a default country is not known, it is detected from the system locale.

The default country is used to validate phone numbers with an ambiguous region.
Assuming a default country of 'US', the view helper will output the following:

```php
echo $this->phoneNumberFormat()->toInternational('2015550123'); // "+1 201-555-0123"
```

The country can be disambiguated by supplying a second parameter to all the [formatting methods listed below](#supported-formats) in the following way:

```php
echo $this->phoneNumberFormat()->toInternational('(0)69-90009 001', 'DE'); // "+49 69 90009001"
```

## Phone Number Validity

The helper expects the provided phone number to be a valid number, either for the configured default country, or for the given country code. Phone numbers that cannot be understood will be returned as-is, i.e.

```php
echo $this->phoneNumberFormat()->toNational('Not a number'); // "Not a number"
// A German phone number with an incorrect country code
echo $this->phoneNumberFormat()->toInternational('(0)69-90009 001', 'US'); // "(0)69-90009 001"
```

## Supported Formats

The following examples use UK phone numbers. Nationally, UK region codes have a leading zero whereas calling the UK from another country necessitates the removal of the leading zero.

### National

Format a phone number for national dialling

```php
echo $this->phoneNumberFormat()->toNational('+441234567890');       // "01234 567890"
echo $this->phoneNumberFormat()->toNational('01234 567 890', 'GB'); // "01234 567890"
```

### International

Format a phone number for international dialling

```php
echo $this->phoneNumberFormat()->toNational('+441234567890');       // "+44 1234 567890"
echo $this->phoneNumberFormat()->toNational('01234 567 890', 'GB'); // "+44 1234 567890"
```

### E.164

Format a phone number to [E.164 format](https://en.wikipedia.org/wiki/E.164)

```php
echo $this->phoneNumberFormat()->toE164('+441234567890');           // "+441234567890"
echo $this->phoneNumberFormat()->toNational('01234 567 890', 'GB'); // "+441234567890"
```

### RFC 3966

Format a phone number to [RFC 3966 format](https://datatracker.ietf.org/doc/html/rfc3966)

```php
echo $this->phoneNumberFormat()->toRfc3966('+441234567890');        // "tel:+44-1234-567890"
echo $this->phoneNumberFormat()->toNational('01234 567 890', 'GB'); // "tel:+44-1234-567890"
```
