<?php declare(strict_types=1);

namespace Vouss\Consul\Services;

use Vouss\Consul\ConsulResponse;

interface KVInterface
{
    public const SERVICE_NAME = 'v1/kv/';

    public function get(string $key, array $options = []): ConsulResponse;

    public function put(string $key, string $value, array $options = []): ConsulResponse;

    public function delete(string $key, array $options = []): ConsulResponse;
}
