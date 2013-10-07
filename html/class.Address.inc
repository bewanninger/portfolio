<?php


class Address {

	public $street_address_1;
	public $street_address_2;

	public $city_name;

	public $subdivision_name;
	protected $_postal_code;
	public $country_name;

	function display()
	{
		$output = "";

	    $output .= $this->street_address_1.'</br>';
	    $output .= $this->street_address_2.'</br>';
	    $output .= $this->city_name.'</br>';
	    $output .= $this->subdivision_name.'</br>';
	    $output .= $this->postal_code.'</br>';
	    $output .= $this->country_name.'</br>';

		return $output;
	}


 }



