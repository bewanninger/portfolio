<?php
namespace Tracker\Form;

use Zend\Form\Form;

class FoodItemForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('food');

        $this->add(array(
            'name' => 'UserId',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'Food',
            'type' => 'Text',
            'options' => array(
                'label' => 'Food Item',
            ),
        ));
        $this->add(array(
            'name' => 'Quantity',
            'type' => 'Text',
            'options' => array(
                'label' => 'Quantity',
            ),
        ));
        $this->add(array(
            'name' => 'Calories',
            'type' => 'Text',
            'options' => array(
                'label' => 'Calories',
            ),
        ));
        $this->add(array(
            'name' => 'Fat',
            'type' => 'Text',
            'options' => array(
                'label' => 'Fat',
            ),
        ));
        $this->add(array(
            'name' => 'Carbs',
            'type' => 'Text',
            'options' => array(
                'label' => 'Carbs',
            ),
        ));
        $this->add(array(
            'name' => 'Protein',
            'type' => 'Text',
            'options' => array(
                'label' => 'Protein',
            ),
        ));
        $this->add(array(
            'name' => 'Alcohol',
            'type' => 'Text',
            'options' => array(
                'label' => 'Alcohol',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Track It!',
                'id' => 'submitbutton',
            ),
        ));
    }
}