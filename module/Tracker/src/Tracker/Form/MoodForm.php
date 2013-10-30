<?php
namespace Tracker\Form;

use Zend\Form\Form;

class MoodForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('mood');

        $this->add(array(
            'name' => 'UserId',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'Mood',
            'type' => 'Text',
            'options' => array(
                'label' => 'What\'s your mood?',
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