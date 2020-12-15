<?php

namespace IptiQ\Domain\Service;

use IptiQ\Domain\Service\ProviderPool;

interface ProviderSelectionStrategy {
    const RANDOM_STRATEGY = 1;
    const ROUND_ROBIN_STRATEGY = 2; 

    public function selectProvider(ProviderPool $providerPool);
}