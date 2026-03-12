# Monitaroo Symfony Bundle

Official Symfony Bundle for [Monitaroo](https://monitaroo.com) - Logs, Metrics & Monitoring.

[![Latest Version](https://img.shields.io/packagist/v/monitaroo/monitaroo-symfony.svg)](https://packagist.org/packages/monitaroo/monitaroo-symfony)
[![PHP Version](https://img.shields.io/packagist/php-v/monitaroo/monitaroo-symfony.svg)](https://packagist.org/packages/monitaroo/monitaroo-symfony)
[![Symfony Version](https://img.shields.io/badge/symfony-2.8%20%7C%203.x%20%7C%204.x%20%7C%205.x%20%7C%206.x%20%7C%207.x-blue.svg)](https://packagist.org/packages/monitaroo/monitaroo-symfony)
[![License](https://img.shields.io/packagist/l/monitaroo/monitaroo-symfony.svg)](https://packagist.org/packages/monitaroo/monitaroo-symfony)

## Installation

```bash
composer require monitaroo/monitaroo-symfony
```

**Requirements:** PHP 7.2+, Symfony 2.8+

## Configuration

### Symfony 4+ (Flex)

Create `config/packages/monitaroo.yaml`:

```yaml
monitaroo:
    api_key: '%env(MONITAROO_API_KEY)%'
```

Add to `.env`:

```env
MONITAROO_API_KEY=mk_your_api_key
```

### Symfony 2.8 / 3.x

Register the bundle in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = [
        // ...
        new Monitaroo\Symfony\MonitarooBundle(),
    ];
}
```

Add configuration in `app/config/config.yml`:

```yaml
monitaroo:
    api_key: '%env(MONITAROO_API_KEY)%'
```

## Quick Start

```php
use Monitaroo\Client;

class MyController
{
    private $monitaroo;

    public function __construct(Client $monitaroo)
    {
        $this->monitaroo = $monitaroo;
    }

    public function index()
    {
        // Logging
        $this->monitaroo->info('User logged in', ['user_id' => 123]);
        $this->monitaroo->error('Payment failed', ['order_id' => 456]);

        // Metrics
        $this->monitaroo->increment('orders.completed');
        $this->monitaroo->gauge('queue.size', 42);
        $this->monitaroo->timing('api.response_time', 145.5);

        // Timer helper
        $stop = $this->monitaroo->startTimer('db.query');
        $users = $this->repository->findAll();
        $stop(); // Records the timing
    }
}
```

## Using with Monolog

Add Monitaroo as a Monolog handler in `config/packages/monolog.yaml`:

```yaml
monolog:
    handlers:
        monitaroo:
            type: service
            id: monitaroo.monolog_handler
            level: debug
```

Then use the standard logger:

```php
use Psr\Log\LoggerInterface;

class MyService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function doSomething()
    {
        $this->logger->info('Something happened');
    }
}
```

## Full Configuration

```yaml
monitaroo:
    # Required: Your API key
    api_key: '%env(MONITAROO_API_KEY)%'

    # API endpoint (default: https://api.monitaroo.com)
    endpoint: 'https://api.monitaroo.com'

    # Service name (default: project directory name)
    service: 'my-app'

    # Environment (default: kernel.environment)
    environment: 'production'

    # Host name (auto-detected if not set)
    host: 'server-01'

    # Batch size before auto-flush (default: 100)
    batch_size: 100

    # Auto-flush on kernel.terminate (default: true)
    auto_flush: true
```

## Logging

### Log Levels

```php
$monitaroo->trace('Detailed trace');
$monitaroo->debug('Debug info');
$monitaroo->info('General info');
$monitaroo->warn('Warning');
$monitaroo->error('Error occurred');
$monitaroo->fatal('Fatal error');
```

### Context & Tags

```php
// Context becomes searchable attributes
$monitaroo->info('Order placed', [
    'order_id' => 123,
    'amount' => 99.99,
]);

// Tags are indexed for fast filtering
$monitaroo->info('Order placed', [
    'order_id' => 123,
    'tags' => [
        'type' => 'order',
        'country' => 'FR',
    ],
]);
```

### Exception Logging

```php
try {
    // ...
} catch (\Exception $e) {
    $monitaroo->error('Operation failed', [
        'exception' => $e,
    ]);
}
```

## Metrics

### Counter

```php
$monitaroo->increment('api.requests');
$monitaroo->increment('items.sold', 5);
$monitaroo->increment('api.requests', 1, ['endpoint' => '/users']);
```

### Gauge

```php
$monitaroo->gauge('queue.size', $queueSize);
$monitaroo->gauge('memory.mb', memory_get_usage(true) / 1024 / 1024);
```

### Timer

```php
// Manual
$start = microtime(true);
$result = $this->doSomething();
$monitaroo->timing('operation.duration', (microtime(true) - $start) * 1000);

// Using helper
$stop = $monitaroo->startTimer('db.query', ['table' => 'users']);
$users = $repository->findAll();
$stop();
```

### Histogram

```php
$monitaroo->histogram('order.amount', $order->getTotal());
```

## Auto-Flush

Logs and metrics are automatically flushed on `kernel.terminate` event, after the response is sent to the client.

To disable auto-flush:

```yaml
monitaroo:
    auto_flush: false
```

Then manually flush:

```php
$monitaroo->flush();
```

## Console Commands

For console commands, logs are flushed when the command finishes (via shutdown handler).

For long-running commands, call `flush()` periodically:

```php
while ($processing) {
    // ... process items
    $monitaroo->flush();
}
```

## License

MIT License. See [LICENSE](LICENSE) for details.
