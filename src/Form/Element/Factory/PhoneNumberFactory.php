<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Form\Element\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\Form\Element\PhoneNumber;
use Psr\Container\ContainerInterface;

use function array_merge;

/**
 * @psalm-import-type Options from PhoneNumber
 */
final class PhoneNumberFactory
{
    /** @param Options|null $options */
    public function __invoke(
        ContainerInterface $container,
        ?string $requestedName = null,
        ?array $options = null
    ): PhoneNumber {
        $componentOptions = Configuration::componentConfig($container);

        /** @psalm-var Options $resolvedOptions */
        $resolvedOptions = array_merge([
            'default_country' => $componentOptions['default-country-code'] ?? null,
            'allowed_types'   => $componentOptions['acceptable-number-types'] ?? null,
        ], $options ?? []);

        return new PhoneNumber(null, $resolvedOptions);
    }
}
