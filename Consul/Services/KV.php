<?php declare(strict_types=1);

namespace Vouss\Consul\Services;

use Vouss\{Consul\Client, Consul\ClientInterface, Consul\ConsulResponse, Consul\OptionsResolver};

final class KV implements KVInterface
{
    private $client;

    /**
     * @va string
     * @param Client|null $client
     */

    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client();
    }

    public function get(string $key, array $options = []): ConsulResponse
    {
        $params = [
            ClientInterface::QUERY => OptionsResolver::resolve($options, ['dc', 'recurse', 'keys', 'separator', 'raw']),
        ];

        return $this->client->get($this->getPath($key), $params);
    }

    public function put(string $key, string $value, array $options = []): ConsulResponse
    {
        $params = [
            ClientInterface::BODY => $value,
            ClientInterface::QUERY => OptionsResolver::resolve($options, ['dc', 'flags', 'cas', 'acquire', 'release']),
        ];

        return $this->client->put($this->getPath($key), $params);
    }

    public function delete(string $key, array $options = []): ConsulResponse
    {
        $params = [
            ClientInterface::QUERY => OptionsResolver::resolve($options, ['dc', 'recurse']),
        ];

        return $this->client->delete($this->getPath($key), $params);
    }

    /**
     * @param string $key
     * @return string
     */
    private function getPath(string $key): string
    {
        return sprintf('%s%s', self::SERVICE_NAME, $key);
    }
}
