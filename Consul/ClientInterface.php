<?php declare(strict_types=1);

namespace Vouss\Consul;

interface ClientInterface
{
    public const REQUEST_PARAMETERS = [
        self::QUERY,
        self::BODY,
    ];

    public const QUERY = 'query';
    public const BODY = 'body';

    public function get(string $url = null, array $options = []);

    public function head(string $url, array $options = []);

    public function delete(string $url, array $options = []);

    public function put(string $url, array $options = []);

    public function patch(string $url, array $options = []);

    public function post(string $url, array $options = []);

    public function options(string $url, array $options = []);
}
