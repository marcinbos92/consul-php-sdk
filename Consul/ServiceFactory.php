<?php declare(strict_types=1);

namespace Vouss\Consul;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Log\LoggerInterface;
use Vouss\{Consul\Services\Agent,
    Consul\Services\AgentInterface,
    Consul\Services\Catalog,
    Consul\Services\CatalogInterface,
    Consul\Services\Health,
    Consul\Services\HealthInterface,
    Consul\Services\KV,
    Consul\Services\KVInterface,
    Consul\Services\Session,
    Consul\Services\SessionInterface};

final class ServiceFactory
{
    /**
     * @var array
     */
    private static $services = [
        AgentInterface::class => Agent::class,
        CatalogInterface::class => Catalog::class,
        HealthInterface::class => Health::class,
        SessionInterface::class => Session::class,
        KVInterface::class => KV::class,

        // for backward compatibility:
        AgentInterface::SERVICE_NAME => Agent::class,
        CatalogInterface::SERVICE_NAME => Catalog::class,
        HealthInterface::SERVICE_NAME => Health::class,
        SessionInterface::SERVICE_NAME => Session::class,
        KVInterface::SERVICE_NAME => KV::class,
    ];

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * ServiceFactory constructor.
     * @param array $options
     * @param LoggerInterface|null $logger
     * @param GuzzleClient|null $guzzleClient
     */
    public function __construct(array $options = [], LoggerInterface $logger = null, GuzzleClient $guzzleClient = null)
    {
        $this->client = new Client($options, $logger, $guzzleClient);
    }

    /**
     * @param string $service
     * @return mixed
     */
    public function get(string $service)
    {
        if (!array_key_exists($service, self::$services)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The service "%s" is not available. Pick one among "%s".',
                    $service,
                    implode('", "', array_keys(self::$services))
                )
            );
        }

        $class = self::$services[$service];

        return new $class($this->client);
    }
}
