<?php

namespace IptiQ\Domain\Service;

use IptiQ\Domain\Model\Provider;

interface HeartBeatChecker {
    public function checkProvider(Provider $provider);
}