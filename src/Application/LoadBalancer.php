<?php

namespace IptiQ\Application;

use IptiQ\Domain\Service\ProviderSelectionStrategy;
use IptiQ\Domain\Model\ProviderSelectionStrategyFactory;
use IptiQ\Domain\Service\HeartBeatChecker;
use IptiQ\Domain\Service\ClusterCapacityControl;
use IptiQ\Domain\Service\ProviderPool;
use IptiQ\Infrastructure\HeartBeatChecker\BasicChecker;

use IptiQ\Domain\Model\Provider;
use \Exception;


class LoadBalancer {
    const DEFAULT_PROVIDER_POOL_SIZE = 10;
    private $providerPool;
    private $heartbeatChecker;
    private $providerHealth;
    private $capacityControl;

    /**
     * Class constructor
     * 
     * @param int $maxPoolSize Maximum number of providers in the pool
     * @param Provider $providerSelectionStrategy Strategy for provider selection.
     * Values are defined in the interface ProviderSelectionStrategy.
     * @param HeartBeatChecked $heartBeatChecker Instance of the HeartBeatChecker
     * implementation to check Provider status
     * @param ClusterCapacityControl $capacityControl Implementation to control
     * the number of requests served by the load balancer
     * 
     */
    public function __construct(
        $maxPoolSize = null, 
        ProviderSelectionStrategy $providerSelectionStretegy = null,
        HeartBeatChecker $heartBeatChecker = null,
        ClusterCapacityControl $capacityControl = null
    ) {
        $this->providerPool = new ProviderPool($maxPoolSize ?? self::DEFAULT_PROVIDER_POOL_SIZE);
        if (!$providerSelectionStretegy) {
            $providerSelectionStretegy = ProviderSelectionStrategyFactory::getSelectionStrategy(ProviderSelectionStrategy::RANDOM_STRATEGY);
        }
        $this->providerSelectionStrategy = $providerSelectionStretegy;
        $this->heartBeatChecker = $heartBeatChecker ?? new BasicChecker();
    }

    /**
     * Method to register a new provider in the pool
     * 
     * @param  Provider $provider Object instance for the provider to register
     * 
     * @return Self reference 
     * @throws Exception When the provider cannot be added because the pool has reached
     * its maximum capacity.
     */
    public function addProvider(Provider $provider) {
        $this->providerPool->addProvider($provider);
        $this->providerHealth[$provider->getId()] = 0;
        
        if ($this->capacityControl) {
            $this->capacityControl->increaseCapacity(1);
        }
        return $this;
    }

    public function get() {
        // Capacity control is perfect fit for an Aspect, so it would be
        // cross-cutting and not tied to any method
        if ($this->capacityControl) {
            $this->capacityControl->serveRequest();
        }

        $retries = 3;
        $result = null;
        do {
            $provider = $this->providerSelectionStrategy->selectProvider($this->providerPool);
            if ($provider) {
                $result = $provider->get();
            }
        } while (!$result && --$retries);

        if (!$provider) {
            if ($this->capacityControl) {
                $this->capacityControl->requestServed();
            }
            throw new Exception('Service not available');
        }

        if ($this->capacityControl) {
            $this->capacityControl->requestServed();
        }

        return $result;
    }
    /**
     * Method to exclude manually a registered provider in the list
     * of active servers in the pool
     * 
     * @param string $providerId Unique identifier of the provider
     * 
     * @return Self-reference if the operation has been successful
     */

    public function excludeProvider($providerId) {
        $this->providerPool->exclude($providerId);
        if ($this->capacityControl) {
            $this->capacityControl->reduceCapacity(1);
        }
        return $this;
    }
    
    /**
     * Method to include manually a registered provider in the list
     * of active servers in the pool
     * 
     * @param string $providerId Unique identifier of the provider
     * 
     * @return Self-reference if the operation has been successful
     */
    public function includeProvider($providerId) {
        $this->providerPool->include($providerId);
        
        if ($this->capacityControl) {
            $this->capacityControl->increaseCapacity(1);
        }
        
        return $this;
    }

    /**
     * Method to be run in a background process to check the current status of
     * the providers.
     * 
     * @TODO: This probably should be better hidden in its own class in the Domain
     */
    public function runHealthCheck() {
        $providers = $this->providerPool->getProviders();
        foreach($provider as $provideId => $provider) {
            $heartBeatOK = $this->heartBeatChecker->check($provider);
            if ($heartBeatOK) {
                if ($this->providerHealth[$providerId] < 0) {
                    $this->providerHealth[$providerId]++;
                    if ($this->providerHealth[$providerId] >= 0) {
                        $this->includeProvider($providerId);
                    }
                }
            } else {
                $this->providerHealth[$providerId] = -2;
                $this->excludeProvider($providerId);
            }
        }
    }
}