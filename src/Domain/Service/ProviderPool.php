<?php

namespace IptiQ\Domain\Service;

use IptiQ\Domain\Model\Provider;
use \Exception;

class ProviderPool {
    private $maxProvidersInPool;
    private $providers;
    private $activeProviders;
    private $excludedProviders;

    public function __construct(int $maxProvidersInPool = 10)  {
        $this->maxProvidersInPool = $maxProvidersInPool;
        $this->providers = array();
        $this->activeProviders = array();
        $this->excludedProviders = array();
    }

    public function getActiveProviders() {
        return array_values($this->activeProviders);
    }

    public function getExcludedProviders() {
        return array_values($this->excludedProviders);
    }

    public function getProviders() {
        return $this->providers;
    }

    public function getAt($position) {
        $providers = array_values($this->activeProviders);
        return $providers[$position] ?? null;
    }

    public function addProvider(Provider $provider) {
        if (count($this->providers) == $this->maxProvidersInPool) {
            throw new Exception('Max number of providers in the pool reached');
        }

        if (!isset($this->providers[$provider->getId()])) {
            $this->providers[$provider->getId()] = $provider;
            $this->activeProviders[$provider->getId()] = $provider;          
        }

        return $provider;
    }

    public function exclude($providerId) {
        if ($this->activeProviders[$providerId]) {
            $this->excludedProviders[$providerId] = $this->activeProviders[$providerId];
            unset($this->activeProviders[$providerId]);
        }
        return $this;       
    }

    public function include($providerId) {
        if ($this->excludedProviders[$providerId]) {
            $this->activeProviders[$providerId] = $this->excludedProviders[$providerId];
            unset($this->excludedProviders[$providerId]);
        }
        return $this;
    }
}