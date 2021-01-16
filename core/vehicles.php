<?php
class Vehicle
{	
	var $owner;
	var $vehicletype;	//Land, Sea, Air
	var $subtype;		//Bus, Truck, Containership...
	var $fuel;
	var $consumption;
	var $builddate;
	var $speed;
	
	var $storage;
	var $seats;
}

class Aircraft extends Vehicle
{
	
}

class Ship extends Vehicle
{
	
}

class Landcraft extends Vehicle
{
	
}
