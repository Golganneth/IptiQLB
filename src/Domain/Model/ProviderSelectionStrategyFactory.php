<?php
namespace IptiQ\Domain\Model;

use IptiQ\Domain\Service\ProviderSelectionStrategy;
use IptiQ\Infrastructure\ProviderSelectionStrategy\RandomStrategy;
use IptiQ\Infrastructure\ProviderSelectionStrategy\RoundRobinStrategy;

class ProviderSelectionStrategyFactory {
    public function getSelectionStrategy(int $strategyType) {
        $strategy = null;
        switch ($strategyType) {
            case ProviderSelectionStrategy::RANDOM_STRATEGY:
                $strategy = new RandomStrategy();
                break;
            case ProviderSelectionStrategy::ROUND_ROBIN_STRATEGY:
                $strategy = new RoundRobinStrategy();
                break;
            default:
                throw new Exception('Unsupported strategy');
                break;
        }
        
        return $strategy;
    }
}