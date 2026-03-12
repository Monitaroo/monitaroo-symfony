<?php

declare(strict_types=1);

namespace Monitaroo\Symfony\EventSubscriber;

use Monitaroo\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Flushes Monitaroo buffers on kernel.terminate.
 */
class FlushSubscriber implements EventSubscriberInterface
{
    /** @var Client */
    private $client;

    /** @var bool */
    private $autoFlush;

    /**
     * @param Client $client
     * @param bool $autoFlush
     */
    public function __construct(Client $client, $autoFlush = true)
    {
        $this->client = $client;
        $this->autoFlush = $autoFlush;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['onKernelTerminate', -100],
        ];
    }

    /**
     * Flush logs and metrics after the response has been sent.
     *
     * @return void
     */
    public function onKernelTerminate()
    {
        if ($this->autoFlush) {
            $this->client->flush();
        }
    }
}
