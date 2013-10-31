<?php
namespace Tracker\Form;

use Zend\Form\Form;

class FoodItemForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('foodItem');

        $this->add(array(
            'name' => 'UserId',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'Name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Food Item',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'Quantity',
            'type' => 'Text',
            'options' => array(
                'label' => 'Quantity',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'Calories',
            'type' => 'Text',
            'options' => array(
                'label' => 'Calories',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'Fat',
            'type' => 'Text',
            'options' => array(
                'label' => 'Fat',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'Carbs',
            'type' => 'Text',
            'options' => array(
                'label' => 'Carbs',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'Protein',
            'type' => 'Text',
            'options' => array(
                'label' => 'Protein',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'Alcohol',
            'type' => 'Text',
            'options' => array(
                'label' => 'Alcohol',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add To Food Log',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary'
            ),
        ));
    }
}