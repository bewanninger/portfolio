<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Resume\Controller\Resume' => 'Resume\Controller\ResumeController',
        ),
    ),

    'router' => array(
         'routes' => array(
             'resume' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/resume[/][:action][/:id][/:quantity]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                         'quantity'     => '[0-9]',
                     ),
                     'defaults' => array(
                         'controller' => 'Resume\Controller\Resume',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

    'view_manager' => array(
        'template_path_stack' => array(
            'resume' => __DIR__ . '/../view',
        ),
    ),

     
);