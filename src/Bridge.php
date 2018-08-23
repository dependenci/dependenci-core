<?php

namespace DependenCI\Core;

/**
 * This class serves as a bridge between DependenCI and the Core classes.
 *
 * @author Miguel Piedrafita <soy@miguelpiedrafita.com>
 */
class Bridge
{
    /**
     * Whether the repository has outdated dependencies.
     *
     * @param string      $composer
     * @param array       $excluded
     *
     * @return bool
     */
    public static function isOutdated(string $composer, $excluded = [])
    {
        $ladder = new Ladder($composer);
        $packages = $ladder->getOutdatedPackages($excluded);
        if (! count($packages)) {
            return false;
        }

        return true;
    }

    /**
     * Get the outdated dependencies of a repository.
     *
     * @param string      $composer
     * @param array       $excluded
     *
     * @return array
     */
    public static function getOutdated(string $composer, $excluded = [])
    {
        $ladder = new Ladder($composer);
        $packages = $ladder->getOutdatedPackages($excluded);
        if (! count($packages)) {
            return [];
        }
        $outdated = [];
        foreach ($packages as $package) {
            $diff = static::versionDiff($package->getVersion(), $package->getLatestVersion());
            if (! $package->isUpgradable()) {
                $outdated[] = [$package->getName(), $package->getVersion(), $diff];
            }
        }

        return $outdated;
    }

    /**
     * Get the diff between the current and latest version.
     *
     * @param string $current
     * @param string $latest
     *
     * @return string
     */
    protected static function versionDiff($current, $latest)
    {
        $needle = 0;

        while ($needle < strlen($current) && $needle < strlen($latest)) {
            if ($current[$needle] !== $latest[$needle]) {
                break;
            }

            $needle++;
        }

        return substr($latest, 0, $needle).substr($latest, $needle);
    }
}
