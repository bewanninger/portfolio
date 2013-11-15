<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Lyrics\Controller\Lyrics' => 'Lyrics\Controller\LyricsController',
        ),
    ),

    'router' => array(
         'routes' => array(
             'lyrics' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/lyrics[/][:action][/:id][/:quantity]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                         'quantity'     => '[0-9]',
                     ),
                     'defaults' => array(
                         'controller' => 'Lyrics\Controller\Lyrics',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

    'view_manager' => array(
        'template_path_stack' => array(
            'lyrics' => __DIR__ . '/../view',
        ),
    ),

     
);