# PhoneNumber

`Laminas\I18n\PhoneNumber\Form\Element\PhoneNumber` is a text input form element that automatically attaches a PhoneNumber validator.

## Basic Usage

This element automatically adds a `type` attribute of value `tel`.

```php
use Laminas\I18n\PhoneNumber\Form\Element\PhoneNumber;
use Laminas\Form\Form;

$phone = new PhoneNumber('number');
$phone->setLabel('Phone Number');
$phone->setAttributes([
    'autocomplete' => 'tel', // 'tel-national' is also an option.
]);

$form = new Form();
$form->add($phone);
```

Using array notation:

```php
use Laminas\I18n\PhoneNumber\Form\Element\PhoneNumber;
use Laminas\Form\Form;

$form = new Form();
$form->add([
    'type' => PhoneNumber::class,
    'name' => 'phone-number',
    'options' => [
        'label' => 'Phone Number',
    ],
    'attributes' => [
        'autocomplete' => 'tel'
    ],
]);
```

TIP: **Autocomplete Attribute**
To make it easier for users to autofill valid information, the [autocomplete attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete) can be used. A value of `tel` should autofill the users phone number with the country dialling code, whereas a value of `tel-national` will autofill the number without the country dialling code. For example:
```php
$form->add([
    'type' => PhoneNumber::class,
    'name' => 'phone-number',
    'attributes' => [
        'autocomplete' => 'tel' // or 'tel-national'
    ],
]);
```

## Available Options

Along with the options relevant to all form elements such as `label`, the phone number input also allows you to pass validator options to the [phone number validator](../validators/phone-number.md):

```php
use Laminas\I18n\PhoneNumber\Form\Element\PhoneNumber;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

$form = new Form();
$form->add([
    'type' => PhoneNumber::class,
    'name' => 'phone-number',
    'options' => [
        'label' => 'Phone Number',
        'default_country' => 'ZA',
        'country_context' => 'country-code-field',
        'allowed_types' => PhoneNumberValue::TYPE_MOBILE,
    ],
    'attributes' => [
        'autocomplete' => 'tel-national'
    ],
]);
$form->add([
    'type' => Text::class,
    'name' => 'country-code-field',
    'options' => [
        'label' => 'Country',
    ],
]);
```

## Public Methods

The following methods are specific to the `PhoneNumber` element; all other methods
defined by the [parent `Element` class](https://docs.laminas.dev/laminas-form/v3/element/element/#public-methods) are also
available.

Method signature                    | Description
----------------------------------- | -----------
`getInputSpecification() : array`   | See write-up below.
`setOptions(array $options) : void` | Set options for an element of type `PhoneNumber`. The accepted options, in addition to the inherited options of [`Laminas\Form\Element`](https://docs.laminas.dev/laminas-form/v3/element/element/#public-methods) are `default_country`, which calls `setDefaultCountry()`, `country_context`, which calls `setCountryContext()` and `allowed_types`, which calls `setAllowedTypes()`.
`setDefaultCountry(string $code): void`  | Sets the default country to validate national phone numbers against.
`setCountryContext(string $inputName): void`              | Sets the name of another input in your form that can be used to determine the country for validating national phone numbers, instead of the default country.
`setAllowedTypes(int $types): void` | Is a bitmask of phone number types to limit what type of number are considered valid.

`getInputSpecification()` returns an input filter specification, which includes the `StringTrim` filter, and a phone number validator configured with the `default_country`, `country_context`, and `allowed_types` options.

The options are described in greater detail in the documentation for the [phone number validator](../validators/phone-number.md).
