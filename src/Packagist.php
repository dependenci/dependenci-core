<?php

namespace DependenCI\Core;

use Packagist\Api\Client;
use GuzzleHttp\Exception\ClientException;

class Packagist extends Client
{
    /**
     * Get a package's latest version.
     *
     * @param string $name
     *
     * @return string|void
     */
    public function getLatestVersion($name)
    {
        try {
            $package = $this->get($name);

            $versions = array_map(function ($version) {
                return $version->getVersion();
            }, $package->getVersions());

            return Version::latest($versions);
        } catch (ClientException $e) {
            return;
        }
    }
}
