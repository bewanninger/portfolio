<?php
namespace Tracker\Form;

use Zend\Form\Form;

class WeightGoalForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('weightGoal');

        $this->setAttribute('class','form-horizontal');
        $this->add(array(
            'name' => 'UserId',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'CurrentWeight',
            'type' => 'Text',
            'options' => array(
                'label' => 'Current Weight',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'GoalWeight',
            'type' => 'Text',
            'options' => array(
                'label' => 'Goal Weight',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'GoalDate',
            'type' => 'Text',
            'options' => array(
                'label' => 'Target Date',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Set Goal Weight!',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }
}