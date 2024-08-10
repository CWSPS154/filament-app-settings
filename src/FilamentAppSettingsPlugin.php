<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentAppSettings;

use Closure;
use CWSPS154\FilamentAppSettings\Page\AppSettings;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAppSettingsPlugin implements Plugin
{
    public static bool $canAccess = true;
    public static array $appAdditionalFields = [];

    public function getId(): string
    {
        return FilamentAppSettingsServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            AppSettings::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function canAccess(bool|Closure $condition): static
    {
        $access = true;
        if ($condition instanceof Closure) {
            $access = $condition();
        }
        self::$canAccess = $access;
        return $this;
    }

    public function appAdditionalField(array $additionalFields):static
    {
        self::$appAdditionalFields = $additionalFields;
        return $this;
    }
}
