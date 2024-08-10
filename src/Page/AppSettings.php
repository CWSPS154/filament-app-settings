<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentAppSettings\Page;

use CWSPS154\FilamentAppSettings\FilamentAppSettingsPlugin;
use CWSPS154\FilamentAppSettings\Settings\Forms\AppForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AppSettings extends Page
{
    protected static string $view = 'filament-app-settings::filament.pages.app-settings';

    public ?array $settings = [];

    public function mount(): void
    {
        $settings = get_settings();
        $this->form->fill($settings);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Save')
                ->label(__('filament-app-settings::app-settings.save'))
                ->color('primary')
                ->submit('save')
        ];
    }

    public static function getTabs(): array
    {
        $tabs = [
            AppForm::getTab(),
        ];
        $classes = self::getClassesInNamespace('Filament\\Settings\\Forms');
        foreach ($classes as $class) {
            if (method_exists($class, 'getTab')) {
                $tabs[] = $class::getTab();
            }
        }
        return $tabs;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs(self::getTabs())
                    ->persistTabInQueryString()
            ])
            ->statePath('settings');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach ($data as $tab => $values) {
            $this->processValues($tab, $values);
        }
        $this->successNotification(__('filament-app-settings::app-settings.save.success'));
        redirect(request()->header('Referer'));
    }

    private function processValues($tab, $values, $prefix = ''): void
    {
        foreach ($values as $field => $value) {
            $key = $prefix ? "{$prefix}.{$field}" : $field;

            if (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    foreach ($value as $index => $subValue) {
                        $this->processValues($tab, $subValue, "{$key}.{$index}");
                    }
                } else {
                    $this->processValues($tab, $value, $key);
                }
            } else {
                \CWSPS154\FilamentAppSettings\Models\AppSettings::updateOrCreate(
                    ['tab' => $tab, 'key' => $key],
                    ['value' => $value]
                );
                $cacheKey = 'settings_data.' . $tab . '.' . $key;
                Cache::forget($cacheKey);
                Cache::forget('settings_data.all');
            }
        }
    }

    private function successNotification(string $title): void
    {
        Notification::make()
            ->title($title)
            ->success()
            ->send();
    }

    public function getLayout(): string
    {
        if (config('filament-app-settings.layout')) {
            return config('filament-app-settings.layout');
        }
        return parent::getLayout();
    }

    public static function getCluster(): ?string
    {
        return config('filament-app-settings.cluster');
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-app-settings.navigation.label'));
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-app-settings.navigation.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-app-settings.navigation.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-app-settings.navigation.sort');
    }

    public static function canAccess(): bool
    {
        return FilamentAppSettingsPlugin::$canAccess;
    }

    protected static function getClassesInNamespace(string $namespace): array
    {
        $path = base_path("app/".str_replace('\\', '/', $namespace));
        $files = File::allFiles($path);
        $classes = [];

        foreach ($files as $file) {
            $class = "App\\".$namespace . '\\' . Str::replaceLast('.php', '', $file->getFilename());
            if (class_exists($class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
