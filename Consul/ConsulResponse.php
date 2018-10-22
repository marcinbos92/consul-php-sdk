<?php declare(strict_types=1);

namespace Vouss\Consul;

final class ConsulResponse
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $status;

    public function __construct(array $headers, string $body, int $status = 200)
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->status = $status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function json()
    {
        return json_decode($this->body, true);
    }
}
