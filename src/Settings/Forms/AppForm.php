<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentAppSettings\Settings\Forms;

use CWSPS154\FilamentAppSettings\FilamentAppSettingsPlugin;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Http;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class AppForm
{
    /**
     * @return Tab
     */
    public static function getTab(): Tab
    {
        return Tab::make('app')
            ->label(__('filament-app-settings::app-settings.form.app'))
            ->icon('heroicon-o-computer-desktop')
            ->schema(array_merge(self::getFields(), self::getAppAdditionalFields()))
            ->columns()
            ->statePath('app');
    }

    public static function getFields(): array
    {
        return [
            self::getAppSection()
        ];
    }

    /**
     * @return Section
     */
    public static function getAppSection(): Section
    {
        return Section::make('System')
            ->label(__('filament-app-settings::app-settings.form.system'))
            ->schema([
                TextInput::make('app_name')
                    ->label(__('filament-app-settings::app-settings.form.field.app.name'))
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(),
                Grid::make()->schema([
                    FileUpload::make('app_logo')
                        ->label(fn () => __('filament-app-settings::app-settings.form.field.app.logo'))
                        ->image()
                        ->directory('assets')
                        ->visibility('public')
                        ->moveFiles()
                        ->imageEditor()
                        ->getUploadedFileNameForStorageUsing(fn () => 'site_logo.png'),
                    FileUpload::make('app_dark_logo')
                        ->label(fn () => __('filament-app-settings::app-settings.form.field.app.dark.logo'))
                        ->image()
                        ->directory('assets')
                        ->visibility('public')
                        ->moveFiles()
                        ->imageEditor()
                        ->getUploadedFileNameForStorageUsing(fn () => 'site_dark_logo.png'),
                    FileUpload::make('app_favicon')
                        ->label(fn () => __('filament-app-settings::app-settings.form.field.app.favicon'))
                        ->image()
                        ->directory('assets')
                        ->visibility('public')
                        ->moveFiles()
                        ->getUploadedFileNameForStorageUsing(fn () => 'site_favicon.ico')
                        ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon']),
                ])->columns(3),
                TextInput::make('support_email')
                    ->label(__('filament-app-settings::app-settings.form.field.app.support.email'))
                    ->prefixIcon('heroicon-o-envelope'),
                PhoneInput::make('support_phone_1')
                    ->label(__('filament-app-settings::app-settings.form.field.app.support.phone.1'))
                    ->rules(['phone'])
                    ->ipLookup(function () {
                        return rescue(fn () => Http::get('https://ipinfo.io/json')->json('country'), app()->getLocale(), report: false);
                    })
                    ->displayNumberFormat(PhoneInputNumberType::NATIONAL),
                PhoneInput::make('support_phone_2')
                    ->label(__('filament-app-settings::app-settings.form.field.app.support.phone.2'))
                    ->rules(['phone'])
                    ->ipLookup(function () {
                        return rescue(fn () => Http::get('https://ipinfo.io/json')->json('country'), app()->getLocale(), report: false);
                    })
                    ->displayNumberFormat(PhoneInputNumberType::NATIONAL),
            ])
            ->columns(3)->collapsible();
    }

    public static function getAppAdditionalFields(): array
    {
        return FilamentAppSettingsPlugin::$appAdditionalFields;
    }
}
