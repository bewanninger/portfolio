<?php

namespace Tracker\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

 class Mood implements InputFilterAwareInterface
 {
     public $id;
     public $mood;
     public $timeStamp;
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['UserId'])) ? $data['UserId'] : null;
         $this->mood = (!empty($data['Mood'])) ? $data['Mood'] : null;
         $this->timeStamp  = (!empty($data['TimeStamp'])) ? $data['TimeStamp'] : null;
     
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
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'Mood',
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