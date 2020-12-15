<?php

namespace IptiQ\Infrastructure\HeartBeatChecker;

use IptiQ\Domain\Service\HeartBeatChecker;
use IptiQ\Domain\Model\Provider;

class BasicChecker implements HeartBeatChecker {
    public function checkProvider(Provider $provider) {
        return ($provider->check() == 'OK');
    }
}