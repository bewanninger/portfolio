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
            ),
            'attributes' => array(
                'id' => 'name',
                'class' => 'typeahead',
            ),
        ));
        $this->add(array(
            'name' => 'Quantity',
            'type' => 'Text',
            'options' => array(
                'label' => 'Quantity',
            ),
            'attributes' => array(
                'id' => 'quantity',
            ),
        ));


        $this->add(array(
            'name' => 'Calories',
            'type' => 'Text',
            'options' => array(
                'label' => 'Calories',
                'label_attributes' => array(
                    'class' => 'data'
                    ),
                ),
            'attributes' => array(
                'class' => 'data',
                'id' => 'calories',
            ),
        ));
        $this->add(array(
            'name' => 'Fat',
            'type' => 'Text',
            'options' => array(
                'label' => 'Fat',
                'label_attributes' => array(
                    'class' => 'data'
                    ),
                ),
            'attributes' => array(
                'class' => 'data',
                'id' => 'fat',
            ),
        ));
        $this->add(array(
            'name' => 'Carbs',
            'type' => 'Text',
            'options' => array(
                'label' => 'Carbs',
                'label_attributes' => array(
                    'class' => 'data'
                    ),
                ),
            'attributes' => array(
                'class' => 'data',
                'id' => 'carbs',
            ),
        ));
        $this->add(array(
            'name' => 'Protein',
            'type' => 'Text',
            'options' => array(
                'label' => 'Protein',
                'label_attributes' => array(
                    'class' => 'data'
                    ),
                ),
            'attributes' => array(
                'class' => 'data',
                'id' => 'protein',
            ),
        ));
        $this->add(array(
            'name' => 'Alcohol',
            'type' => 'Text',
            'options' => array(
                'label' => 'Alcohol',
                'label_attributes' => array(
                    'class' => 'data'
                    ),
                ),
            'attributes' => array(
                'class' => 'data',
                'id' => 'alcohol',
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