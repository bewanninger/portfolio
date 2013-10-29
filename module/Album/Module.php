<?php

namespace Album;


 // Add these import statements:
 use Album\Model\Album;
 use Album\Model\AlbumTable;
 use Album\Model\Drawing;
 use Album\Model\DrawingTable;
 use Album\Model\User;
 use Album\Model\UserTable;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway;

 class Module
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }

    public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'Album\Model\AlbumTable' =>  function($sm) {
                     $tableGateway = $sm->get('AlbumTableGateway');
                     $table = new AlbumTable($tableGateway);
                     return $table;
                 },
                 'Album\Model\DrawingTable' =>  function($sm) {
                     $tableGateway = $sm->get('DrawingTableGateway');
                     $table = new DrawingTable($tableGateway);
                     return $table;
                 },
                 'Album\Model\UserTable' =>  function($sm) {
                     $tableGateway = $sm->get('UserTableGateway');
                     $table = new UserTable($tableGateway);
                     return $table;
                 },
                 'AlbumTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('album');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Album());
                     return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                 },
                 'DrawingTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('album');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Drawing());
                     return new TableGateway('Drawing', $dbAdapter, null, $resultSetPrototype);
                 },
                 'UserTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('album');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new User());
                     return new TableGateway('User', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
 }