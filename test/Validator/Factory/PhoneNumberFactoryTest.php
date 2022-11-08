<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Validator\Factory;

use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\I18n\PhoneNumber\Validator\Factory\PhoneNumberFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PhoneNumberFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private ContainerInterface $container;
    private PhoneNumberFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory   = new PhoneNumberFactory();
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

    public function testOptionsAreMergedWithDefaults(): void
    {
        $this->configWillBe([
            'laminas-i18n-phone-number' => [
                'default-country-code'    => 'GB',
                'acceptable-number-types' => PhoneNumberValue::TYPE_RECOMMENDED,
            ],
        ]);

        $validator = ($this->factory)($this->container, null, [
            'allowed_types' => PhoneNumberValue::TYPE_EMERGENCY,
        ]);

        self::assertFalse($validator->isValid('911'));
        self::assertFalse($validator->isValid('+4401234567890'));
        self::assertTrue($validator->isValid('999'));
    }

    public function testThatValidatorOptionsCanBeNull(): void
    {
        ($this->factory)($this->container);
        self::assertTrue(true);
    }
}
