<?php

namespace Emmanuelikeogu\DevGuard;



use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Installer\PackageEvent;

class DevGuardUninstallPlugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io) {}
    public function deactivate(Composer $composer, IOInterface $io) {}
    public function uninstall(Composer $composer, IOInterface $io) {}

    public static function getSubscribedEvents()
    {
        return [
            'pre-package-uninstall' => 'onPreUninstall',
            'post-package-uninstall' => 'onPostUninstall',
        ];
    }

    public function onPreUninstall(PackageEvent $event)
    {
        $package = $event->getOperation()->getPackage();
        if ($package->getName() === 'emmanuelikeogu/devguard') {
            // Run cleanup BEFORE uninstall
            if (file_exists('artisan')) {
                exec('php artisan devguard:cleanup --force');
            }
        }
    }

    public function onPostUninstall(PackageEvent $event)
    {
        $package = $event->getOperation()->getPackage();
        if ($package->getName() === 'emmanuelikeogu/devguard') {
            // Remove 3rd party packages
            exec('composer remove opcodesio/log-viewer laravel/telescope dedoc/scramble --no-interaction');
        }
    }
}
