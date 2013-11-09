<?php
namespace Tracker;

use Tracker\Model\Mood;
use Tracker\Model\MoodTable;
use Tracker\Model\FoodLog;
use Tracker\Model\FoodLogTable;
use Tracker\Model\FoodItem;
use Tracker\Model\FoodItemTable;
use Tracker\Model\User;
use Tracker\Model\UserTable;
use Tracker\Model\WeightGoal;
use Tracker\Model\WeightGoalTable;
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
                 'Tracker\Model\MoodTable' =>  function($sm) {
                     $tableGateway = $sm->get('MoodTableGateway');
                     $table = new MoodTable($tableGateway);
                     return $table;
                 },
                 'MoodTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('tracker');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Mood());
                     return new TableGateway('Mood', $dbAdapter, null, $resultSetPrototype);
                 },
                 'Tracker\Model\FoodLogTable' =>  function($sm) {
                     $tableGateway = $sm->get('FoodLogTableGateway');
                     $table = new FoodLogTable($tableGateway);
                     return $table;
                 },
                 'FoodLogTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('tracker');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new FoodLog());
                     return new TableGateway('FoodLog', $dbAdapter, null, $resultSetPrototype);
                 },
                 'Tracker\Model\FoodItemTable' =>  function($sm) {
                     $tableGateway = $sm->get('FoodItemTableGateway');
                     $table = new FoodItemTable($tableGateway);
                     return $table;
                 },
                 'FoodItemTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('tracker');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new FoodItem());
                     return new TableGateway('FoodItem', $dbAdapter, null, $resultSetPrototype);
                 },
                 'Tracker\Model\UserTable' =>  function($sm) {
                     $tableGateway = $sm->get('UserTableGateway');
                     $table = new UserTable($tableGateway);
                     return $table;
                 },
                 'UserTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('tracker');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new User());
                     return new TableGateway('User', $dbAdapter, null, $resultSetPrototype);
                 },
                 'Tracker\Model\WeightGoalTable' =>  function($sm) {
                     $tableGateway = $sm->get('UserTableGateway');
                     $table = new WeightGoalTable($tableGateway);
                     return $table;
                 },
                 'WeightGoalTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('tracker');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new WeightGoal());
                     return new TableGateway('WeightGoal', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
}