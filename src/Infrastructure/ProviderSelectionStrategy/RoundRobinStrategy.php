<?php

namespace IptiQ\Infrastructure\ProviderSelectionStrategy;

use IptiQ\Domain\Service\ProviderSelectionStrategy;
use IptiQ\Domain\Service\ProviderPool;

class RoundRobinStrategy {
    private $lastSelection;

    public function __construct() {
        $this->lastSelection = -1;
    }

    public function selectProvider(ProviderPool $providerPool) {
        $selectedProvider = null;
        if ($providerPool->getSize() > 0) {
            $position = (++$this->lastSelection) % $providerPool->getSize();
            $selectedProvider = $providerPool->getAt($position);
        }
        return $selectedProvider;
    }
}