<?php

use PHPUnit\Framework\TestCase;

use IptiQ\Application\LoadBalancer;
use IptiQ\Domain\Model\Provider;

class LoadBalancerTest extends TestCase {
    public function testAddProvider_calledFirstTime_AddProviderToLB() {
        $sut = new LoadBalancer(1);
        $response = $sut->addProvider(new Provider());
        $this->assertEquals($sut, $response);
    }

    public function testAddProvider_addingMoreThanMaxProviders_WillThrowException() {
        $sut = new LoadBalancer(1);
        $this->expectException(\Exception::class);

        $sut->addProvider(new Provider());
        $sut->addProvider(new Provider());
    }

    public function testGet_calledWithSingleProvider_WillReturnProviderId() {
        $sut = new LoadBalancer(1);
        $provider = new Provider();
        $sut->addProvider($provider);

        $response = $sut->get();
        
        $this->assertEquals($provider->getId(), $response);
    }

    public function testGet_calledWithNoProviders_WillReturnException() {
        $sut = new LoadBalancer(1);
        $this->expectException(\Exception::class);
        $sut->get();
    }

    public function testGet_AfterAllProvidersExcluded_WillReturnException() {
        $sut = new LoadBalancer(1);
        $this->expectException(\Exception::class);

        $provider = new Provider();
        $sut->addProvider($provider);
        $sut->excludeProvider($provider->getId());
        $response = $sut->get();
    }

    public function testGet_AfterAllProvidersRemovedExceptOne_WillReturnActiveProviderId() {
        $sut = new LoadBalancer(1);
        $this->expectException(\Exception::class);

        $provider1 = new Provider();
        $provider2 = new Provider();

        $sut->addProvider($provider1);
        $sut->addProvider($provider2);
        $sut->excludeProvider($provider2->getId());

        $response = $sut->get();
        $this->assertEquals($provider1->getId(), $response);
    }
}