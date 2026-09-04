<?php

declare(strict_types=1);

namespace LaminasBench\I18n\PhoneNumber;

use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;

/**
 * This benchmark is effectively testing the validation of the country code option.
 *
 * In previous versions, it used Laminas\I18n\CountryCode::tryFromString() which generates stack traces and is very slow
 * as a result. The validator now inspects the option to see if it looks like a country code as opposed to a locale
 * string first, to avoid the generation of a stack trace during option validation.
 */
#[Revs(100)]
#[Iterations(20)]
#[Warmup(2)]
final readonly class ValidatorConstructBench
{
    public function benchConstructWithNoOptions(): void
    {
        new PhoneNumber([]);
    }

    public function benchConstructWithValidCountryOption(): void
    {
        new PhoneNumber([
            'country' => 'GB',
        ]);
    }
}
