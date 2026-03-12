<?php

declare(strict_types=1);

namespace Monitaroo\Symfony\Factory;

use Monitaroo\Client;
use Monitaroo\Monitaroo;

/**
 * Factory for creating Monitaroo Client instances.
 */
class ClientFactory
{
    /**
     * Create a Monitaroo Client instance.
     *
     * @param string $apiKey
     * @param string $endpoint
     * @param string|null $service
     * @param string|null $environment
     * @param string|null $host
     * @param int $batchSize
     * @param bool $autoFlush
     * @param string $rootDir kernel.root_dir (app/ in Symfony 2.8, or project_dir in newer)
     * @param string $kernelEnvironment
     * @return Client
     */
    public static function create(
        $apiKey,
        $endpoint,
        $service,
        $environment,
        $host,
        $batchSize,
        $autoFlush,
        $rootDir,
        $kernelEnvironment
    ) {
        // Default service name from project directory
        // In Symfony 2.8, root_dir is /path/to/project/app
        // In Symfony 4+, it would be project_dir
        if (empty($service)) {
            $projectDir = dirname($rootDir); // Go up from app/ to project root
            $service = basename($projectDir);
        }

        // Default environment from kernel
        if (empty($environment)) {
            $environment = $kernelEnvironment;
        }

        // Default host
        if (empty($host)) {
            $host = gethostname() ?: '';
        }

        return Monitaroo::init([
            'apiKey' => $apiKey,
            'endpoint' => $endpoint,
            'service' => $service,
            'environment' => $environment,
            'host' => $host,
            'batchSize' => $batchSize,
            'autoFlush' => $autoFlush,
        ]);
    }
}
