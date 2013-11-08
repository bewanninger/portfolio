<?php #login form ?><?php
namespace Tracker\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('login-form');

        $this->setAttribute('class','form-signin');

        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Username',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Login',
                'id' => 'submitbutton',
                'class' => 'btn btn-large btn-primary',
            ),
        ));
    }
}