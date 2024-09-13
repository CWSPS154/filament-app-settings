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
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentAppSettingsPlugin implements Plugin
{
    use EvaluatesClosures;

    /**
     * @var bool|Closure|mixed
     */
    protected bool|array $canAccess = true;
    public static array $appAdditionalFields = [];

    public function getId(): string
    {
        return FilamentAppSettingsServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            AppSettings::class
        ])->brandName(get_settings('app.app_name') ?? config('app_name'))
            ->darkModeBrandLogo(asset('storage/' . get_settings("app.app_dark_logo")))
            ->brandLogo(asset('storage/' . get_settings("app.app_logo")))
            ->favicon(asset('storage/' . get_settings("app.app_favicon")));
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function canAccess(bool|Closure|string $ability = true, $arguments = null): static
    {
        if ($ability instanceof Closure) {
            $this->canAccess = $this->evaluate($ability);
        } elseif (is_string($ability) && !is_null($arguments)) {
            $this->canAccess = [
                'ability' => $ability,
                'arguments' => $arguments,
            ];
        } else {
            $this->canAccess = (bool)$ability;
        }
        return $this;
    }

    public function getCanAccess(): array|bool
    {
        return $this->canAccess;
    }

    public function appAdditionalField(array $additionalFields): static
    {
        self::$appAdditionalFields = $additionalFields;
        return $this;
    }
}
