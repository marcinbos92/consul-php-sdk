<?php declare(strict_types=1);

namespace Vouss\Consul;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Psr\Log\{LoggerInterface, NullLogger};
use Vouss\Consul\Exception\{ClientException, ServerException};

final class Client implements ClientInterface
{
    /**
     * @var HttpClientInterface | null
     */
    private $client;

    /**
     * @var LoggerInterface | null
     */
    private $logger;

    /**
     * @var string
     */
    private const BASE_CONSUL_URI = 'http://127.0.0.1:8500';

    public function __construct(array $options = [], LoggerInterface $logger = null, HttpClientInterface $client = null)
    {
        $baseUri = self::BASE_CONSUL_URI;
        if (isset($options['base_uri'])) {
            $baseUri = $options['base_uri'];
        } else {
            if (getenv('CONSUL_HTTP_ADDR') !== false) {
                $baseUri = getenv('CONSUL_HTTP_ADDR');
            }
        }
        $options = array_replace([
            'base_uri' => $baseUri,
            'http_errors' => false,
        ], $options);
        $this->client = $client ?: new GuzzleClient($options);
        $this->logger = $logger ?: new NullLogger();
    }

    public function get(string $url = null, array $options = []): ConsulResponse
    {
        return $this->doRequest('GET', $url, $options);
    }

    public function head(string $url, array $options = []): ConsulResponse
    {
        return $this->doRequest('HEAD', $url, $options);
    }

    public function delete(string $url, array $options = []): ConsulResponse
    {
        return $this->doRequest('DELETE', $url, $options);
    }

    public function put(string $url, array $options = []): ConsulResponse
    {
        return $this->doRequest('PUT', $url, $options);
    }

    public function patch(string $url, array $options = []): ConsulResponse
    {
        return $this->doRequest('PATCH', $url, $options);
    }

    public function post(string $url, array $options = []): ConsulResponse
    {
        return $this->doRequest('POST', $url, $options);
    }

    public function options(string $url, array $options = []): ConsulResponse
    {
        return $this->doRequest('OPTIONS', $url, $options);
    }

    private function doRequest(string $method, ?string $url, array $options): ConsulResponse
    {
        if (isset($options['body']) && is_array($options['body'])) {
            $options['body'] = \json_encode($options['body']);
        }
        $this->logger->info(sprintf('%s "%s"', $method, $url));
        $this->logger->debug(sprintf('Requesting %s %s', $method, $url), array('options' => $options));
        try {
            $response = $this->client->request($method, $url, $options);
        } catch (TransferException $e) {
            $message = sprintf('Something went wrong when calling consul (%s).', $e->getMessage());
            $this->logger->error($message);
            throw new ServerException($message);
        }
        $this->logger->debug(sprintf("Response:\n%s", $this->formatResponse($response)));
        if (400 <= $response->getStatusCode()) {
            $message = sprintf('Something went wrong when calling consul (%s - %s).', $response->getStatusCode(), $response->getReasonPhrase());
            $this->logger->error($message);
            $message .= "\n" . (string)$response->getBody();
            if (500 <= $response->getStatusCode()) {
                throw new ServerException($message, $response->getStatusCode());
            }
            throw new ClientException($message, $response->getStatusCode());
        }

        return new ConsulResponse($response->getHeaders(), (string)$response->getBody(), $response->getStatusCode());
    }

    private function formatResponse(Response $response): string
    {
        $headers = [];
        foreach ($response->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $headers[] = sprintf('%s: %s', $key, $value);
            }
        }

        return sprintf("%s\n\n%s", implode("\n", $headers), $response->getBody());
    }
}
