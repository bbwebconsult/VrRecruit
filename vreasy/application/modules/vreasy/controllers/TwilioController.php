<?php

use Vreasy\Models\Task;
use Vreasy\Utils\Twilio;

class Vreasy_TwilioController extends Vreasy_Rest_Controller
{

    protected $message;
    
    public function preDispatch()
    {
        parent::preDispatch();
        
         $req = $this->getRequest();
        $action = $req->getActionName();
               
        if( !in_array($action, [
                'update',
                ]) && !$this->tasks && !$this->task->id) {
                    throw new Zend_Controller_Action_Exception('Resource not found', 404);
                }
    
    }

    public function updateAction()
    {
        
        $req = $this->getRequest();
        /*
         * TODO: How do we know what task the provider is replying to (admitting there is more than one)?
         * For now, just consider it is the pending task with the shortest deadline and was created first
        */
        $tasks = Task::where([    'state' => Task::STATE_PENDING,
                                  'assigned_phone' => $req->getParam('From'),
                                  'deadline' => '> NOW()', //ignore passed tasks
                              ],
                              [   'orderBy' => ['deadline', 'created_at'],
                                  'orderDirection' => ['asc', 'desc'],
                                  'limit' => 1
                              ]);
        
        if($tasks !== array())
        {
            $task = array_pop($tasks);
            switch(Twilio::decodeAnswer($req->getParam('Body')))
            {
                case Twilio::ANSWER_POSITIVE:
                    $task->state = Task::STATE_ACCEPTED;
                break;
                case Twilio::ANSWER_NEGATIVE:
                    $task->state = Task::STATE_REFUSED;
                break;
            }
                    
            $task->updated_at = gmdate(DATE_FORMAT);
            $task->save();
            
            $this->view->response = ['error' => false, 'task' => $task];
        }
        else
        {
            $this->view->response = ['error' => true, 'message' => 'NO task was found assigned to the incoming number'];
        }

    }
    
}