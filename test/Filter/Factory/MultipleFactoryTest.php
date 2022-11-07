<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Filter\Factory;

use Laminas\I18n\PhoneNumber\Filter\Factory\ToE164Factory;
use Laminas\I18n\PhoneNumber\Filter\Factory\ToInternationalPhoneNumberFactory;
use Laminas\I18n\PhoneNumber\Filter\Factory\ToNationalPhoneNumberFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MultipleFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
    }

    private function countryCodeIs(string $code): void
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
                    'default-country-code' => $code,
                ],
            ]);
    }

    public function testThatTheE164FactoryWillProvideTheExpectedCountryCode(): void
    {
        $this->countryCodeIs('GB');
        $factory = new ToE164Factory();
        $filter  = $factory($this->container);
        self::assertEquals(
            '+441234567890',
            $filter->filter('01234 567 890')
        );
    }

    public function testThatTheNationalFactoryWillProvideTheExpectedCountryCode(): void
    {
        $this->countryCodeIs('GB');
        $factory = new ToNationalPhoneNumberFactory();
        $filter  = $factory($this->container);
        self::assertEquals(
            '01234 567890',
            $filter->filter('01234567890')
        );
    }

    public function testThatTheInternationalFactoryWillProvideTheExpectedCountryCode(): void
    {
        $this->countryCodeIs('GB');
        $factory = new ToInternationalPhoneNumberFactory();
        $filter  = $factory($this->container);
        self::assertEquals(
            '+44 1234 567890',
            $filter->filter('01234 567 890')
        );
    }
}
