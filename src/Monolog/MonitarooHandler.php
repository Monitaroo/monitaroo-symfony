<?php

declare(strict_types=1);

namespace Monitaroo\Symfony\Monolog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monitaroo\Client;

/**
 * Monolog handler that sends logs to Monitaroo.
 *
 * Usage in monolog.yaml:
 *
 * monolog:
 *     handlers:
 *         monitaroo:
 *             type: service
 *             id: monitaroo.monolog_handler
 *             level: debug
 */
class MonitarooHandler extends AbstractProcessingHandler
{
    /** @var Client */
    private $client;

    /**
     * @param Client $client
     * @param int|string $level
     * @param bool $bubble
     */
    public function __construct(Client $client, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
    }

    /**
     * @inheritDoc
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {
        $level = $this->mapLevel($record['level']);
        $message = $record['message'];
        $context = isset($record['context']) ? $record['context'] : [];

        // Add extra data to context
        if (!empty($record['extra'])) {
            $context['extra'] = $record['extra'];
        }

        // Add channel as tag
        if (!empty($record['channel'])) {
            if (!isset($context['tags'])) {
                $context['tags'] = [];
            }
            $context['tags']['channel'] = $record['channel'];
        }

        $this->client->log($level, $message, $context);
    }

    /**
     * Map Monolog level to Monitaroo level.
     *
     * @param int $level
     * @return string
     */
    private function mapLevel($level)
    {
        switch ($level) {
            case Logger::DEBUG:
                return 'debug';
            case Logger::INFO:
                return 'info';
            case Logger::NOTICE:
                return 'info';
            case Logger::WARNING:
                return 'warn';
            case Logger::ERROR:
                return 'error';
            case Logger::CRITICAL:
            case Logger::ALERT:
            case Logger::EMERGENCY:
                return 'fatal';
            default:
                return 'info';
        }
    }
}
