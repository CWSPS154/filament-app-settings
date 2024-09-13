<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentAppSettings;

use CWSPS154\FilamentAppSettings\Commands\CreateSettingsTab;
use CWSPS154\FilamentAppSettings\Database\Seeders\DatabaseSeeder;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAppSettingsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-app-settings';

    public function configurePackage(Package $package) : void
    {
        $package->name(self::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_app_settings_table')
            ->hasCommand(CreateSettingsTab::class)
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->startWith(function(InstallCommand $command) {
                        $command->info('Hi Mate, Thank you for installing Filament App Settings.');
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function(InstallCommand $command) {
                        if ($command->confirm('Do you wish to run the seeder for cwsps154/filament-users-roles-permissions ?')) {
                            $command->comment('The seeder is filled with "admin" as panel id, please check the route name for your panel');
                            $command->comment('Running seeder...');
                            $command->call('db:seed', [
                                'class' => DatabaseSeeder::class
                            ]);
                        }
                        $command->info('I hope this package will help you to build custom settings with desired filament form Components/Input');
                    })
                    ->askToStarRepoOnGitHub('CWSPS154/filament-app-settings');
            });
    }

    public function boot(): FilamentAppSettingsServiceProvider
    {
        require_once __DIR__ . '/helper.php';
        return parent::boot();
    }
}
