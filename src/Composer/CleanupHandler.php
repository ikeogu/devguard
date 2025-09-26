<?php

namespace Emmanuelikeogu\DevGuard\Composer;

use Composer\Script\Event;
use Illuminate\Support\Facades\Artisan;

class CleanupHandler
{
    public static function prePackageUninstall(Event $event)
    {
        $io = $event->getIO();

        // Make sure we're uninstalling *this* package
        $operations = $event->getComposer()->getInstallationManager();
        $packageName = 'emmanuelikeogu/devguard';

        $commandLine = implode(' ', $_SERVER['argv'] ?? []);
        if (strpos($commandLine, "remove {$packageName}") === false) {
            return; // not our uninstall
        }

        $io->write("<info>Running DevGuard cleanup before uninstall...</info>");

        try {
            passthru('php artisan devguard:cleanup --force');
        } catch (\Throwable $e) {
            $io->writeError("<error>DevGuard cleanup failed: {$e->getMessage()}</error>");
        }
    }
}
