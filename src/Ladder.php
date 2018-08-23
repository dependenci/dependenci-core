<?php

namespace DependenCI\Core;

/**
 * This class serves as a bridge between the Composer files & Packagist.
 *
 * @author Miguel Piedrafita <soy@miguelpiedrafita.com>
 */
class Ladder
{
    /**
     * The composer instance.
     *
     * @var \DependenCI\Core\Composer
     */
    protected $composer;

    /**
     * Create a new ladder instance.
     *
     * @param string $composer
     *
     * @return void
     */
    public function __construct($composer)
    {
        $this->composer = new Composer($composer);
    }

    /**
     * Get outdated packages with their current and latest version.
     *
     * @param array $excluded
     *
     * @return array
     */
    public function getOutdatedPackages(array $excluded = [])
    {
        return $this->processPackages(
            $this->composer->getRequiredPackages($excluded),
            $excluded
        );
    }

    /**
     * Process the packages into an outdated array.
     *
     * @param array $required
     * @param array $excluded
     *
     * @return array
     */
    protected function processPackages(array $required, array $excluded)
    {
        $outdated = [];

        foreach ($required as $package) {
            $name = $package['name'];
            $version = Version::normalize($package['version']);
            $prettyVersion = $required[$name]['version'];
            $devDependency = $package['devDependency'];

            if (in_array($name, $excluded)) {
                continue;
            }

            $package = new Package($name, $version, $prettyVersion, $devDependency);

            if ($package->isOutdated()) {
                $outdated[] = $package;
            }
        }

        return $outdated;
    }
}
