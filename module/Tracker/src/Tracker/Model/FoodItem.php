<?php

namespace Tracker\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

 class FoodItem implements InputFilterAwareInterface
 {
     public $foodId;
     public $name;
     public $calories;
     public $fat;
     public $protein;
     public $carbs;
     public $alcohol;
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->foodId     = (!empty($data['FoodId'])) ? $data['FoodId'] : null;
         $this->name = (!empty($data['Name'])) ? $data['Name'] : null;
         $this->calories = (!empty($data['Calories'])) ? $data['Calories'] : null;
         $this->fat = (!empty($data['Fat'])) ? $data['Fat'] : null;
         $this->protein = (!empty($data['Protein'])) ? $data['Protein'] : null;
         $this->carbs = (!empty($data['Carbs'])) ? $data['Carbs'] : null;
         $this->alcohol  = (!empty($data['Alcohol'])) ? $data['Alcohol'] : null;
     
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
                'name'     => 'UserId',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'Quantity',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array( 
                    array( 
                        'name' => 'Between', 
                        'options' => array( 
                            'min' => 1, 
                            'max' => 50, 
                        ), 
                    ), 
                ), 
            ));
            $inputFilter->add(array(
                'name'     => 'Calories',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'Fat',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'Carbs',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'Protein',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'Name',
                'required' => True,
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
                            'max'      => 50,
                        ),
                    ),
                ),
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
 }