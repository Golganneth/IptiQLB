<?php
namespace IptiQ\Domain\Model;

class Provider {
    private $id;

    public function __constructor() {
        $this->id = uniqid('pr_');
    }

    public function getId() {
        return $this->id;
    }  

    public function get() {
        return $this->id;
    }
    
    public function check() {
        return 'OK';
    }
}