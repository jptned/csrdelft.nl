<?php

require_once 'view/GoogleCallbackView.class.php';

class GoogleController extends AclController
{
    public function __construct($query)
    {
        parent::__construct($query, null);
        $this->acl = array(
            'callback' => 'P_LOGGED_IN'
        );
    }

    public function performAction(array $args = array())
    {
        $this->action = $this->getParam(2);
        $args = array(
            'state' => $this->getParam('state'),
            'code' => $this->getParam('code'),
            'error' => $this->getParam('error')
        );
        return parent::performAction($args); // TODO: Change the autogenerated stub
    }

    public function callback($state, $code, $error)
    {
        $this->view = new GoogleCallbackView($state, $code, $error);
    }
}