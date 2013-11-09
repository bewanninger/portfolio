<?php

namespace Tracker\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

 class WeightGoal implements InputFilterAwareInterface
 {
     public $id;
     public $currentWeight;
     public $goalWeight;
     public $goalDate;
     public $timeStamp;

     protected $inputFilter;

     public function exchangeArray($data)
     {
        $this->id            = (!empty($data['UserId'])) ? $data['UserId'] : null;
        $this->currentWeight = (!empty($data['CurrentWeight'])) ? $data['CurrentWeight'] : null;
        $this->goalWeight    = (!empty($data['GoalWeight'])) ? $data['GoalWeight'] : null;
        $this->goalDate      = (!empty($data['GoalDate'])) ? $data['GoalDate'] : null;
        $this->timeStamp     = (!empty($data['TimeStamp'])) ? $data['TimeStamp'] : null;
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
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
/*
            $inputFilter->add(array(
                'name'     => 'GoalWeight',
                'required' => true,
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
                            'max'      => 15,
                        ),
                    ),
                ),
            ));
*/

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
 }