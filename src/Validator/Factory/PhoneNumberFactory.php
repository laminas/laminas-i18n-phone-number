<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Validator\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;
use Psr\Container\ContainerInterface;

use function array_merge;

/**
 * @psalm-import-type Options from PhoneNumber
 */
final class PhoneNumberFactory
{
    /** @param Options $options */
    public function __invoke(
        ContainerInterface $container,
        ?string $requestedName = null,
        array $options = []
    ): PhoneNumber {
        $componentOptions = Configuration::componentConfig($container);

        /** @psalm-var Options $resolvedOptions */
        $resolvedOptions = array_merge([
            'country'       => $componentOptions['default-country-code'] ?? null,
            'allowed_types' => $componentOptions['acceptable-number-types'] ?? null,
        ], $options);

        return new PhoneNumber($resolvedOptions);
    }
}
