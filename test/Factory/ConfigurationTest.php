<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Factory;

use Laminas\I18n\PhoneNumber\CountryCode;
use Locale;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ConfigurationTest extends TestCase
{
    private TestFactory $factory;
    /** @var MockObject&ContainerInterface */
    private ContainerInterface $container;
    private string $preserveLocale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory        = new TestFactory();
        $this->container      = $this->createMock(ContainerInterface::class);
        $this->preserveLocale = Locale::getDefault();
    }

    protected function tearDown(): void
    {
        Locale::setDefault($this->preserveLocale);
        parent::tearDown();
    }

    /** @return array<array-key, array{0: array}> */
    public function nullConfigProvider(): array
    {
        return [
            [[]],
            [['laminas-i18n-phone-number' => []]],
            [['laminas-i18n-phone-number' => ['default-country-code' => null]]],
        ];
    }

    /** @dataProvider nullConfigProvider */
    public function testGetCountryCodeForConfigurationSetupsThatResultInNull(array $config): void
    {
        Locale::setDefault('de_DE');
        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $expect = CountryCode::fromString('DE');
        self::assertTrue(
            $expect->equals(
                $this->factory->getDefaultCountry($this->container)
            )
        );
    }

    public function testThatCountryCodeUsesTheDefaultLocaleWhenThereIsNoConfig(): void
    {
        Locale::setDefault('uk_UA');
        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $this->container->expects(self::never())
            ->method('get');

        $expect = CountryCode::fromString('UA');
        self::assertTrue(
            $expect->equals(
                $this->factory->getDefaultCountry($this->container)
            )
        );
    }

    public function testExpectedCountryCodeIsReturned(): void
    {
        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'laminas-i18n-phone-number' => [
                    'default-country-code' => 'ZA',
                ],
            ]);

        $expect = CountryCode::fromString('ZA');
        self::assertTrue(
            $expect->equals(
                $this->factory->getDefaultCountry($this->container)
            )
        );
    }
}
