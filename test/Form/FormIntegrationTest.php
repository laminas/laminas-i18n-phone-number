<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Form;

use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\Form\FormElementManager;
use Laminas\I18n\PhoneNumber\Form\Element\PhoneNumber;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\I18n\PhoneNumber\Test\NumberGeneratorTrait;
use Laminas\I18n\PhoneNumber\Test\ProjectIntegrationTestCase;
use Psr\Container\ContainerInterface;

use function assert;

final class FormIntegrationTest extends ProjectIntegrationTestCase
{
    use NumberGeneratorTrait;

    private ContainerInterface $container;
    private Form $form;
    private FormElementManager $formElements;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = self::getContainer();

        $formElements = $this->container->get(FormElementManager::class);
        /** @psalm-suppress RedundantCondition */
        assert($formElements instanceof FormElementManager);
        $form = $formElements->get(Form::class);
        assert($form instanceof Form);
        $this->form         = $form;
        $this->formElements = $formElements;
    }

    public function testThePhoneNumberElementCanBeRetrievedFromTheFormElementManager(): void
    {
        self::assertInstanceOf(PhoneNumber::class, $this->formElements->get(PhoneNumber::class));
    }

    /** @dataProvider validPhoneNumberProvider */
    public function testThatGivenACountryContextThePhoneNumberHasTheExpectedValue(
        string $number,
        string $country,
        int $type,
    ): void {
        if (($type & PhoneNumberValue::TYPE_RECOMMENDED) === 0) {
            return;
        }

        $this->form->add([
            'name' => 'country',
            'type' => Text::class,
        ]);
        $this->form->add([
            'name'    => 'number',
            'type'    => PhoneNumber::class,
            'options' => [
                'country_context' => 'country',
                'default_country' => null,
            ],
        ]);

        $this->form->setData([
            'country' => $country,
            'number'  => $number,
        ]);

        self::assertTrue($this->form->isValid());
        self::assertEquals([
            'country' => $country,
            'number'  => $number,
        ], $this->form->getData());
    }

    /** @return list<array{0: string}> */
    public function validE164Provider(): array
    {
        return [
            ['+44 (0) 1234 567 890'],
            ['+44 1234 567 890'],
            ['+441234567890'],
        ];
    }

    /** @dataProvider validE164Provider */
    public function testThatAValidE164WithNoOtherContextIsConsideredValid(string $input): void
    {
        $this->form->add([
            'name'    => 'number',
            'type'    => PhoneNumber::class,
            'options' => [
                'country_context' => null,
                'default_country' => null,
            ],
        ]);

        $this->form->setData([
            'number' => $input,
        ]);

        self::assertTrue($this->form->isValid());
        self::assertEquals([
            'number' => $input,
        ], $this->form->getData());
    }

    /** @dataProvider validPhoneNumberProvider */
    public function testThatTheCountryContextTakesPrecedenceOverTheDefaultCountry(
        string $number,
        string $country,
        int $type,
    ): void {
        if (($type & PhoneNumberValue::TYPE_RECOMMENDED) === 0) {
            return;
        }

        $this->form->add([
            'name' => 'country',
            'type' => Text::class,
        ]);
        $this->form->add([
            'name'    => 'number',
            'type'    => PhoneNumber::class,
            'options' => [
                'country_context' => 'country',
                'default_country' => 'US',
            ],
        ]);

        $this->form->setData([
            'country' => $country,
            'number'  => $number,
        ]);

        self::assertTrue($this->form->isValid());
        self::assertEquals([
            'country' => $country,
            'number'  => $number,
        ], $this->form->getData());
    }

    public function testThatTheDefaultCountryIsUsedWhenTheContextHasNoValue(): void
    {
        $this->form->add([
            'name' => 'country',
            'type' => Text::class,
        ]);
        $this->form->add([
            'name'    => 'number',
            'type'    => PhoneNumber::class,
            'options' => [
                'country_context' => 'country',
                'default_country' => 'GB',
            ],
        ]);

        $this->form->setData([
            'country' => '',
            'number'  => '01234 567 890',
        ]);

        self::assertTrue($this->form->isValid());
        self::assertEquals([
            'country' => '',
            'number'  => '01234 567 890',
        ], $this->form->getData());
    }

    public function testPhoneNumberValidityWithoutContextAndMismatchingDefaultCountry(): void
    {
        $this->form->add([
            'name' => 'country',
            'type' => Text::class,
        ]);
        $this->form->add([
            'name'    => 'number',
            'type'    => PhoneNumber::class,
            'options' => [
                'country_context' => 'country',
                'default_country' => 'US',
            ],
        ]);

        $this->form->setData([
            'country' => '',
            'number'  => '01234 567 890',
        ]);

        self::assertFalse($this->form->isValid());
        $messages = $this->form->getMessages();
        self::assertArrayHasKey('number', $messages);
    }

    public function testThatNoElementOptionsAreRequiredForValidationAgainstADefaultCountry(): void
    {
        $container = self::getContainer([
            'laminas-i18n-phone-number' => [
                'default-country-code' => 'SE',
            ],
        ]);

        $form = $container->get(FormElementManager::class)->get(Form::class);
        self::assertInstanceOf(Form::class, $form);

        $form->add([
            'name' => 'number',
            'type' => PhoneNumber::class,
        ]);
        $form->setData(['number' => '031-3900600']);
        self::assertTrue($form->isValid());
    }
}
