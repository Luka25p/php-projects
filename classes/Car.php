<?php

class Car 
{
    // properties / fields
    private $brand;
    private $color;
    private $vehicleType = "car";

    // constructor
    public function __construct($brand, $color = "none"){
        $this->brand = $brand;
        $this->color = $color;
    }
}

$car01 = new Car("volvo","green");
$car02 = new Car("BMW");