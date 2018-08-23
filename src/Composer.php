<?php

namespace DependenCI\Core;

/**
 * This class parses the composer.json file.
 *
 * @author Miguel Piedrafita <soy@miguelpiedrafita.com>
 */
class Composer
{
    /**
     * The composer.json contents.
     *
     * @var string
     */
    protected $composer;

    /**
     * Create a new composer instance.
     *
     * @param string $composer
     *
     * @return void
     */
    public function __construct(string $composer)
    {
        $this->composer = json_decode($composer, true);
    }

    /**
     * Get required package versions.
     *
     * @param array $excluded
     *
     * @throws \LogicException
     *
     * @return array
     */
    public function getRequiredPackages(array $excluded = [])
    {
        return $this->processPackages(
            $this->composer,
            $this->getExcludedPackages($excluded)
        );
    }

    /**
     * Process packages to a package array.
     *
     * @param array $content
     * @param array $excluded
     *
     * @return array
     */
    protected function processPackages(array $content, array $excluded)
    {
        $packages = [];

        foreach (['require', 'require-dev'] as $key) {
            if (! isset($content[$key])) {
                continue;
            }

            foreach ($content[$key] as $name => $version) {
                if (! strstr($name, '/')) {
                    continue;
                }

                if (in_array($name, $excluded)) {
                    continue;
                }

                $packages[$name] = [
                  'name'          => $name,
                  'version'       => $version,
                  'devDependency' => $key === 'require-dev',
                ];
            }
        }

        return $packages;
    }

    /**
     * Get excluded packages.
     *
     * @param array $excluded
     *
     * @return array
     */
    public function getExcludedPackages(array $excluded = [])
    {
        if (! isset($this->composer['dependenci']['excluded'])) {
            return $excluded;
        }

        return array_merge($excluded, $this->composer['extra']['dependenci']['excluded']);
    }
}
