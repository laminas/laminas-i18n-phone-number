<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\View\Helper\Factory;

use Laminas\I18n\PhoneNumber\View\Helper\Factory\PhoneNumberFormatFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PhoneNumberFormatFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private ContainerInterface $container;
    private PhoneNumberFormatFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory   = new PhoneNumberFormatFactory();
    }

    /** @param array<array-key, mixed> $config */
    private function configWillBe(array $config): void
    {
        $this->container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);
    }

    public function testThatTheHelperWillBeConfiguredWithADefaultCountry(): void
    {
        $this->configWillBe([
            'laminas-i18n-phone-number' => [
                'default-country-code' => 'GB',
            ],
        ]);

        $helper = ($this->factory)($this->container);
        self::assertEquals(
            '+441234567890',
            $helper->toE164('01234 567 890')
        );
    }

    public function testThatTheHelperWillNotHaveADefaultCountry(): void
    {
        $this->configWillBe([]);

        $helper = ($this->factory)($this->container);
        self::assertEquals(
            '01234 567 890',
            $helper->toE164('01234 567 890')
        );
    }
}
