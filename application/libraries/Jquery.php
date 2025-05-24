<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/** jQuery_Action */
//require_once 'jQuery/Action.php';
/** jQuery_Element */
//require_once 'jQuery/Element.php';

/**
 * jQuery
 *
 * @author Anton Shevchuk
 * @access   public
 * @package  jQuery
 * @version  0.8
 */
class Jquery
{
    /**
     * static var for realize singlton
     * @var jQuery
     */
    public static $jQuery;
    
    /**
     * response stack
     * @var array
     */
    public $response = array(
                              // actions (addMessage, addError, eval etc.)
                              'a' => array(),
                              // jqueries
                              'q' => array()
                            );
    /**
     * __construct
     *
     * @access  public
     */
    function __construct() 
    {
    	
    }
    
    /**
     * init
     * init singleton if needed
     *
     * @return void
     */
    public static function init()
    {
        if (empty(Jquery::$jQuery)) {
            Jquery::$jQuery = new jQuery();
        }
        return true;
    }


    /**
     * addData
     *
     * add any data to response
     *
     * @param string $key
     * @param mixed $value
     * @param string $callBack
     * @return jQuery
     */
    public static function addData ($key, $value, $callBack = null)
    {
        Jquery::init();

        $jQuery_Action = new jQuery_Action();
        $jQuery_Action ->add('k', $key);
        $jQuery_Action ->add('v', $value);
        
        // add call back func into response JSON obj
        if ($callBack) {
            $jQuery_Action ->add("callback", $callBack);
        }

        Jquery::addAction(__FUNCTION__, $jQuery_Action);

        return Jquery::$jQuery;
    }

    /**
     * addMessage
     * 
     * @param string $msg
     * @param string $callBack
     * @param array  $params
     * @return jQuery
     */
    public static function addMessage ($msg, $callBack = null, $params = null)
    {
        Jquery::init();
        
        $jQuery_Action = new jQuery_Action();        
        $jQuery_Action ->add("msg", $msg);
        
        
        // add call back func into response JSON obj
        if ($callBack) {
            $jQuery_Action ->add("callback", $callBack);
        }
        
        if ($params) {
            $jQuery_Action ->add("params",  $params);
        }
        
        Jquery::addAction(__FUNCTION__, $jQuery_Action);
        
        return Jquery::$jQuery;
    }
    
    /**
     * addError
     * 
     * @param string $msg
     * @param string $callBack
     * @param array  $params
     * @return jQuery
     */
    public static function addError ($msg, $callBack = null, $params = null)
    {
        Jquery::init();
        
        $jQuery_Action = new jQuery_Action();        
        $jQuery_Action ->add("msg", $msg);

        // add call back func into response JSON obj
        if ($callBack) {
            $jQuery_Action ->add("callback", $callBack);
        }
        
        if ($params) {
            $jQuery_Action ->add("params",  $params);
        }
        
        Jquery::addAction(__FUNCTION__, $jQuery_Action);
        
        return Jquery::$jQuery;
    }
    /**
     * evalScript
     *      
     * @param  string $foo
     * @return jQuery
     */
    public static function evalScript ($foo)
    {
        Jquery::init();
        
        $jQuery_Action = new jQuery_Action();        
        $jQuery_Action ->add("foo", $foo);

        Jquery::addAction(__FUNCTION__, $jQuery_Action);
        
        return Jquery::$jQuery;
    }
    
    /**
     * response
     * init singleton if needed
     *
     * @return string JSON
     */
    public static function getResponse()
    {
        Jquery::init();
        
        echo json_encode(Jquery::$jQuery->response);
        exit ();
    }
    
    /**
     * addQuery
     * add query to stack
     *
     * @return jQuery_Element
     */
    public static function addQuery($selector)
    {
        Jquery::init();
        
        return new jQuery_Element($selector);
    }
    
    /**
     * addQuery
     * add query to stack
     * 
     * @param  jQuery_Element $jQuery_Element
     * @return void
     */
    public static function addElement(jQuery_Element &$jQuery_Element)
    {
        Jquery::init();
        
        array_push(Jquery::$jQuery->response['q'], $jQuery_Element);
    }
    
        
    /**
     * addAction
     * add query to stack
     * 
     * @param  string $name
     * @param  jQuery_Action $jQuery_Action
     * @return void
     */
    public static function addAction($name, jQuery_Action &$jQuery_Action)
    {
        Jquery::init();
        
        Jquery::$jQuery->response['a'][$name][] = $jQuery_Action;
    }
}

/**
 * jQuery_Element - class for work with jQuery framework
 *
 * @author Anton Shevchuk
 * @access   public
 * @package  jQuery
 */
class jQuery_Element
{
    /**
     * selector path
     * @var string
     */
    public $s;
    
    /**
     * methods
     * @var array
     */
    public $m = array();
    
    /**
     * args
     * @var array
     */
    public $a = array();
    
    /**
     * __construct
     * contructor of jQuery
     *
     * @return jQuery_Element
     */
    public function __construct($selector)
    {
        Jquery::addElement($this); 
        $this->s = $selector;
    }
    
    /**
     * __call
     *
     * @return jQuery_Element
     */
    public function __call($method, $args)
    {
        array_push($this->m, $method);
        array_push($this->a, $args);
        
        return $this;
    }
    
    /**
     * end
     * need to create new jQuery
     *
     * @return jQuery_Element
     */
    public function end()
    {
        return new jQuery_Element($this->s);
    }
}

/**
 * Class jQuery_Action
 *
 * Abstract class for any parameter of any action
 *
 * @author Anton Shevchuk
 * @access   public
 * @package  jQuery
 */
class jQuery_Action
{
    /**
     * add param to list
     * 
     * @param  string $param
     * @param  string $value
     * @return jQuery_Action
     */
    public function add($param, $value)
    {
        $this->$param = $value;
        return $this;
    }
}


/**
 * jQuery
 *
 * alias for Jquery::jQuery
 *
 * @access  public
 * @param   string   $selector
 * @return  jQuery_Element
 */
function ajaxQuery($selector) 
{
    return Jquery::addQuery($selector);
}

function j($selector) 
{
    return Jquery::addQuery($selector);
}
/**
 * *
 *
 * @param string $string
 * @return jQuery_Element
 */
function ajaxEvalScript($string)
{
	return Jquery::evalScript($string);
}

/**
 * 
 *
 * @return unknown
 */
function ajaxResponse()
{
	return Jquery::getResponse();
}

/**
 * Enter description here...
 *
 * @param unknown_type $message
 * @return unknown
 */
function ajaxAlert($message)
{
	return Jquery::addMessage($message);
}

function ajaxRedirect($url)
{
	ajaxEvalScript ("document.location.href= '$url'");
}

function ajaxError($msg, $selector = "#msgerrorbox") 
{
	ajaxEvalScript('$("#dialogBox").dialog("open");');
    return ajaxQuery($selector)->html($msg);
}