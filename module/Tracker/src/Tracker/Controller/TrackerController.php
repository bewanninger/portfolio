<?php
namespace Tracker\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;
use Tracker\Form\MoodForm;
use Tracker\Model\Mood;
use Tracker\Model\MoodTable;



class TrackerController extends AbstractActionController
{
	protected $sessionContainer;
	protected $moodTable;


	public function __construct()
    {
        $this->sessionContainer = new Container('session');
    }

	public function indexAction(){
		$moodForm = new MoodForm();
        $moods = $this->getMoodTable()->getMood(1);

        $request = $this->getRequest();
        #echo var_dump($request);
        #die;
        if ($request->isPost()) {
            $newMood = new Mood();
            $moodForm->setInputFilter($newMood->getInputFilter());
            $moodForm->setData($request->getPost());

            if ($moodForm->isValid()){
                #echo var_dump($moodForm->getData());
                #die;
                $newMood->exchangeArray($moodForm->getData());
                $this->getMoodTable()->saveMood($newMood);
            }
            // Redirect to list of albums
            //return $this->redirect()->toRoute('album');
            return array('moodForm' => $moodForm,
             'mood' => $moods,
             );
        }
		return array('moodForm' => $moodForm,
			 'mood' => $moods,
			 );
	}
    
    public function getMoodTable()
         {
            $table = "Mood";
             if (!$this->moodTable) {
                 $sm = $this->getServiceLocator();
                 $this->moodTable = $sm->get('Tracker\Model\MoodTable');
             }
             return $this->moodTable;
         }
}