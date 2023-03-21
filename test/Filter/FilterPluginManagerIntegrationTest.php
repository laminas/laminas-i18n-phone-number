<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Filter;

use Laminas\Filter\FilterPluginManager;
use Laminas\I18n\PhoneNumber\ConfigProvider;
use Laminas\I18n\PhoneNumber\Filter\AbstractFilter;
use Laminas\I18n\PhoneNumber\Filter\ToE164;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FilterPluginManagerIntegrationTest extends TestCase
{
    private FilterPluginManager $pluginManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pluginManager = new FilterPluginManager(new ServiceManager());
        $config              = (new ConfigProvider())->__invoke()['filters'];
        $this->pluginManager->configure($config);
    }

    /** @return array<string, array{0: class-string<AbstractFilter>, 1: string}> */
    public static function filterClassProvider(): array
    {
        return [
            'E164'          => [ToE164::class, 'toE164'],
            'International' => [ToInternationalPhoneNumber::class, 'toInternationalPhoneNumber'],
            'National'      => [ToNationalPhoneNumber::class, 'toNationalPhoneNumber'],
        ];
    }

    /**
     * @param class-string<AbstractFilter> $expectedClassName
     */
    #[DataProvider('filterClassProvider')]
    public function testFiltersCanBeRetrievedByAlias(string $expectedClassName, string $alias): void
    {
        self::assertInstanceOf(
            $expectedClassName,
            $this->pluginManager->get($alias),
        );
    }

    /**
     * @param class-string<AbstractFilter> $expectedClassName
     */
    #[DataProvider('filterClassProvider')]
    public function testFiltersCanBeRetrievedByClassName(string $expectedClassName): void
    {
        self::assertInstanceOf(
            $expectedClassName,
            $this->pluginManager->get($expectedClassName),
        );
    }

    public function testThatFiltersAreNotSharedByDefault(): void
    {
        self::assertNotSame(
            $this->pluginManager->get(ToE164::class),
            $this->pluginManager->get(ToE164::class),
        );
    }

    public function testThatFiltersWillAcceptOptionsToCustomiseTheCountryCode(): void
    {
        $de = $this->pluginManager->get(ToE164::class, ['country-code' => 'DE']);
        $au = $this->pluginManager->get(ToE164::class, ['country-code' => 'AU']);

        self::assertSame('+493023125000', $de->filter('(0)30-23125 000'));
        self::assertSame('+61255501234', $au->filter('(02) 5550 1234'));
    }
}
