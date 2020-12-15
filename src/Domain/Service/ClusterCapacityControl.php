<?php

namespace IptiQ\Domain\Service;


class ClusterCapacityControl {
    private $currentCapacity;
    private $totalProviders;
    private $requestsPerProvider;

    public function __construct($requestsPerProvider = 1000) {
        if (!int($requestsPerProvider) || $requestsPerProvider <= 0) {
            throw new Exception('Invalid number of request per provider');
        } 

        $this->requestsPerProvider = $requestPerProvider;
        $this->maxCapacity = 0;
        $this->currentCapacity = 0;
        $this->totalProviders = 0;
    }

    public function increaseMaxCapacity($numberNewProviders) {
        if (!int($numberNewProviders) || $numberNewProviders <= 0) {
            throw new Exception('Invalid number of providers');
        }
        $this->totalProviders += $numberNewServers;
        return $this;
    }
    public function reduceMaxCapacity($numberRemovedServers) {
        if (   !int($numberRemovedServers) 
            || $numberNewServers <= 0
            || $numberRemovedServers > $this->totalProviders
        ) {
            throw new Exception('Invalid number of providers');
        }

        $this->totalProviders -= $numberRemovedServers;
        return $this;
    }

    private function calculateMaxCapacity() {
        return $this->totalProviders * $this->requestsPerProvider;
    }

    public function serveRequest() {
        if ($this->currentCapacity >= $this->calculateMaxCapacity()) {
            throw new Exception('Max capacity reached');
        }
        $this->currentCapacity++;
        return $this;
    }

    public function requestServed() {
        // Yes, this needs to be atomic    
        $this->currentCapacity--;
        return $this;
    }
}