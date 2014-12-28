<?php

use Vreasy\Models\TaskHistory;

class Vreasy_TaskhistoryController extends Vreasy_Rest_Controller
{
    protected $taskHistory;

    public function preDispatch()
    {
        parent::preDispatch();
        $req = $this->getRequest();
        $action = $req->getActionName();
        $contentType = $req->getHeader('Content-Type');
        
        if($req->getParam('format') == 'json') {
            switch ($action) {
                case 'show':
                    $this->taskHistory = TaskHistory::where(['id_task' => $req->getParam('id')]);
                    break;
            }
        }

        if( !in_array($action, [
                'show',
            ]) && !$this->taskHistory) {
            throw new Zend_Controller_Action_Exception('Resource not found', 404);
        }

    }

    public function showAction()
    {
        $this->view->taskHistory = $this->taskHistory;
        $this->_helper->conditionalGet()->sendFreshWhen(['etag' => $this->taskHistory]);
    }

}
