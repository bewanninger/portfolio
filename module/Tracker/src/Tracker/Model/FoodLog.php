<?php

namespace Tracker\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

 class FoodLog implements InputFilterAwareInterface
 {
     public $userId;
     public $foodId;
     public $date;
     public $quantity;
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['UserId'])) ? $data['UserId'] : null;
         $this->foodId = (!empty($data['foodId'])) ? $data['foodId'] : null;
         $this->date  = (!empty($data['Date'])) ? $data['Date'] : null;
         $this->quantity  = (!empty($data['Quantity'])) ? $data['Quantity'] : null;
     
         //$this->user  = (!empty($data['user'])) ? $data['user'] : null;
     }

     public function getArrayCopy()
    {
        return get_object_vars($this);
    }


     public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'FoodId',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'Mood',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
 }