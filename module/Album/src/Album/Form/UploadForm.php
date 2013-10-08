<?php
namespace Album\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class UploadForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        // File Input
        $file = new Element\File('image-file');
        $file->setLabel('Daily Image Upload')
             ->setAttribute('id', 'image-file');
        $this->add($file);

        $text = new Element\Text('text');
        $text->setLabel('Title');
        $this->add($text);
    }

    public function addInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // File Input
        $fileInput = new InputFilter\FileInput('image-file');
        $fileInput->setRequired(true);
        $fileInput->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'    => './html/img/upload',
                'use_upload_name' => true,
                'overwrite'       => true,
            )
        );
        $inputFilter->add($fileInput);


        // Text Input
        $text = new InputFilter\Input('text');
        $text->setRequired(true);
        $inputFilter->add($text);

        $this->setInputFilter($inputFilter);
    }

    
}