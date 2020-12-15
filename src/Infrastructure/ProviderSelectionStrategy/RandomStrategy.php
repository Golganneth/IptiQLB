<?php

namespace IptiQ\Infrastructure\ProviderSelectionStrategy;

use IptiQ\Domain\Service\ProviderSelectionStrategy;
use IptiQ\Domain\Service\ProviderPool;

class RandomStrategy implements ProviderSelectionStrategy {
    public function selectProvider(ProviderPool $providerPool) {
        $activeProviders = $providerPool->getActiveProviders();
        return $activeProviders[rand(0, count($activeProviders)-1)] ?? null;
    }
}