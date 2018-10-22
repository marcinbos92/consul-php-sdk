<?php declare(strict_types=1);

namespace Vouss\Consul\Services;

use Vouss\{Consul\Client, Consul\ConsulResponse, Consul\OptionsResolver};

final class Agent implements AgentInterface
{
    private $client;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client();
    }

    public function checks(): ConsulResponse
    {
        return $this->client->get($this->getPath('/checks'));
    }

    public function services(): ConsulResponse
    {
        return $this->client->get($this->getPath('/services'));
    }

    public function members(array $options = []): ConsulResponse
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['wan']),
        ];

        return $this->client->get($this->getPath('/members'), $params);
    }

    public function self(): ConsulResponse
    {
        return $this->client->get($this->getPath('/self'));
    }

    public function join(string $address, array $options = []): ConsulResponse
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['wan']),
        ];

        return $this->client->get($this->getPath(sprintf('/join/%s', $address)), $params);
    }

    public function forceLeave(string $node): ConsulResponse
    {
        return $this->client->get($this->getPath(sprintf('/force-leave/%s', $node)));
    }

    public function registerCheck(string $check): ConsulResponse
    {
        $params = [
            'body' => $check,
        ];

        return $this->client->put($this->getPath('/check/register'), $params);
    }

    public function deregisterCheck(string $checkId): ConsulResponse
    {
        return $this->client->put($this->getPath(sprintf('/check/deregister/%s', $checkId)));
    }

    public function passCheck(string $checkId, array $options = []): ConsulResponse
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['note']),
        ];

        return $this->client->put($this->getPath(sprintf('/check/pass/%s', $checkId)), $params);
    }

    public function warnCheck(string $checkId, array $options = []): ConsulResponse
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['note']),
        ];

        return $this->client->put($this->getPath(sprintf('/check/pass/warn/%s', $checkId)), $params);
    }

    public function failCheck(string $checkId, array $options = []): ConsulResponse
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['note']),
        ];

        return $this->client->put($this->getPath(sprintf('/check/pass/fail/%s', $checkId)), $params);
    }

    public function registerService(string $service): ConsulResponse
    {
        $params = [
            'body' => $service,
        ];

        return $this->client->put($this->getPath('/service/register'), $params);
    }

    public function deregisterService(string $serviceId): ConsulResponse
    {
        return $this->client->put($this->getPath(sprintf('/service/deregister/%s', $serviceId)));
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
