<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber;

final class Module
{
    public function getConfig(): array
    {
        return (new ConfigProvider())();
    }
}
