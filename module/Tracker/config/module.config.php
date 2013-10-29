<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Tracker\Controller\Tracker' => 'Tracker\Controller\TrackerController',
        ),
    ),

    'router' => array(
         'routes' => array(
             'tracker' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/tracker[/][:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Tracker\Controller\Tracker',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

    'view_manager' => array(
        'template_path_stack' => array(
            'tracker' => __DIR__ . '/../view',
        ),
    ),
    /*
    'db' => array(
         'driver'         => 'Pdo',
         'dsn'            => 'mysql:dbname=jharvard_tracker;host=zend',
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),
     ),
     'service_manager' => array(
         'factories' => array(
             'Zend\Db\Adapter\Adapter'
                     => 'Zend\Db\Adapter\AdapterServiceFactory',
         ),
     ),
     */


     
);