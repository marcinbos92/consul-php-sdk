<?php declare(strict_types=1);

namespace Vouss\Consul\Services;

use Vouss\Consul\ConsulResponse;

interface AgentInterface
{
    const SERVICE_NAME = '/v1/agent';

    public function checks(): ConsulResponse;

    public function services(): ConsulResponse;

    public function members(array $options = []): ConsulResponse;

    public function self(): ConsulResponse;

    public function join(string $address, array $options = []): ConsulResponse;

    public function forceLeave(string $node): ConsulResponse;

    public function registerCheck(string $check): ConsulResponse;

    public function deregisterCheck(string $checkId): ConsulResponse;

    public function passCheck(string $checkId, array $options = []): ConsulResponse;

    public function warnCheck(string $checkId, array $options = []): ConsulResponse;

    public function failCheck(string $checkId, array $options = []): ConsulResponse;

    public function registerService(string $service): ConsulResponse;

    public function deregisterService(string $serviceId): ConsulResponse;
}
