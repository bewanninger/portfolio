<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Chat\Controller\Chat' => 'Chat\Controller\ChatController',
        ),
    ),

    'router' => array(
         'routes' => array(
             'chat' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/chat[/][:action]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                     ),
                     'defaults' => array(
                         'controller' => 'Chat\Controller\Chat',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

    'view_manager' => array(
        'template_path_stack' => array(
            'chat' => __DIR__ . '/../view',
        ),
    ),


);
