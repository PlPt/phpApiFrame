<?php

/**
ApiFrame class for processing and creating easy Rest APIs in PHP.



*/
class OutputData
{
    
    public $data;
    public $status;
    private $startTime;
    private $endTime;
    public $elapsedTime;
    public $errorData;
    
    function __construct()
    {
        
    }
    
    function setStartTime($time)
    {
        if (!isset($this->startTime))
            $this->startTime = $time;
    }
    
    function setEndTime($timee)
    {
        if (!isset($this->endTime))
            $this->endTime = $timee;
    }
    
    function setErrorData($errCode, $errDetails, $errMessage, $function, $information)
    {
        $this->errorData = array(
            "errorCode" => $errCode,
            "errorDetails" => $errDetails,
            "errorMessage" => $errMessage,
            "function" => $function,
            "information" => $information
        );
    }
    
    function evaluate()
    {
        
        $this->elapsedTime = $this->endTime - $this->startTime;
    }
    
    
    
    
}



// Frame Class for APIs
class Frame
{
    
    public $debug = false;
    public $out;
    public static $_instance = null;
    
	/**
	Constructor initialises the Frame default data
	*/
    function __construct()
    {
        
        
        $this->out = new OutputData();
        $this->out->setStartTime(microtime(true));
        Frame::$_instance = $this;
        
        
        
    }
    
	/**
	Start frame timer
	*/
    function Start()
    {
        $this->out = new OutputData();
        $this->out->setStartTime(microtime(true));
        
        
    }
    
    /**
	Write a string to a logfile
	*/
    function log($logstring, $loglevel = "")
    {
        writeTextFile('./log_' . date("j.n.Y") . '.txt', $logstring);
    }
    
    /**
	Intern method for setting the HTTP Status code
	*/
    function http_response_code($code = NULL)
    {
        
        
        
        if ($code !== NULL) {
            
            switch ($code) {
                case 100:
                    $text = 'Continue';
                    break;
                case 101:
                    $text = 'Switching Protocols';
                    break;
                case 200:
                    $text = 'OK';
                    break;
                case 201:
                    $text = 'Created';
                    break;
                case 202:
                    $text = 'Accepted';
                    break;
                case 203:
                    $text = 'Non-Authoritative Information';
                    break;
                case 204:
                    $text = 'No Content';
                    break;
                case 205:
                    $text = 'Reset Content';
                    break;
                case 206:
                    $text = 'Partial Content';
                    break;
                case 300:
                    $text = 'Multiple Choices';
                    break;
                case 301:
                    $text = 'Moved Permanently';
                    break;
                case 302:
                    $text = 'Moved Temporarily';
                    break;
                case 303:
                    $text = 'See Other';
                    break;
                case 304:
                    $text = 'Not Modified';
                    break;
                case 305:
                    $text = 'Use Proxy';
                    break;
                case 400:
                    $text = 'Bad Request';
                    break;
                case 401:
                    $text = 'Unauthorized';
                    break;
                case 402:
                    $text = 'Payment Required';
                    break;
                case 403:
                    $text = 'Forbidden';
                    break;
                case 404:
                    $text = 'Not Found';
                    break;
                case 405:
                    $text = 'Method Not Allowed';
                    break;
                case 406:
                    $text = 'Not Acceptable';
                    break;
                case 407:
                    $text = 'Proxy Authentication Required';
                    break;
                case 408:
                    $text = 'Request Time-out';
                    break;
                case 409:
                    $text = 'Conflict';
                    break;
                case 410:
                    $text = 'Gone';
                    break;
                case 411:
                    $text = 'Length Required';
                    break;
                case 412:
                    $text = 'Precondition Failed';
                    break;
                case 413:
                    $text = 'Request Entity Too Large';
                    break;
                case 414:
                    $text = 'Request-URI Too Large';
                    break;
                case 415:
                    $text = 'Unsupported Media Type';
                    break;
                case 500:
                    $text = 'Internal Server Error';
                    break;
                case 501:
                    $text = 'Not Implemented';
                    break;
                case 502:
                    $text = 'Bad Gateway';
                    break;
                case 503:
                    $text = 'Service Unavailable';
                    break;
                case 504:
                    $text = 'Gateway Time-out';
                    break;
                case 505:
                    $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }
            
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            
            header($protocol . ' ' . $code . ' ' . $text);
            
            $GLOBALS['http_response_code'] = $code;
            
        } else {
            
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            
        }
        
        return $code;
        
    }
    
    
    
    
    
    
    
    
    
    /**
	External method for setting the http status code 
	*/
    function setHttpStatusCode($httpstatuscode)
    {
        
        
        
     $this->http_response_code($httpstatuscode);
    }
    
	/**
	Enables the error reporting in the frame, which allows to get php errors via api
	*/
    function enableErrorReporting($handleErrors = false)
    {
        error_reporting(0);
        
        // Register handler
        if ($handleErrors) {
            set_error_handler("Frame::error_handler");
        }
        set_exception_handler("Frame::error_handler");
        register_shutdown_function("Frame::error_handler");
        
        
    }
    
    /**
	Static error handlers
	*/
    static function error_handler()
    {
        // Check for unhandled errors (fatal shutdown)
        $e = error_get_last();
			
        
        // If none, check function args (error handler)
        if ($e === null)
            $e = func_get_args();
        
        // Return if no error
        if (empty($e))
            return;
        
        // "Normalize" exceptions (exception handler)
        if ($e[0] instanceof Exception) {
            call_user_func_array(__FUNCTION__, array(
                $e[0]->getCode(),
                $e[0]->getMessage(),
                $e[0]->getFile(),
                $e[0]->getLine(),
                $e[0]
            ));
            return;
        }
        
        // Create with consistent array keys
        $e = array_combine(array(
            'number',
            'message',
            'file',
            'line',
            'context'
        ), array_pad($e, 5, null));
        
        // Output error page
        
        
        $number  = $e["number"];
        $message = $e["message"];
        $line    = $e["line"];
        $file    = $e["file"];
        $context = $e["context"];
		//exit(var_dump($e));
        $errors  = date("d.m.y H:m:s") . ": Error($number): $message in $file on Line $line with \n";
		if (!headers_sent() && $number <8)
		{
        $ot      = new OutputData();
        $ot->setErrorData($number, $message, "", "??", $errors);
        $ot->status = "Error";
        $ot->evaluate();
		$ot->data = $e;
        ob_clean ();
        exit(json_encode($ot));
		}
        
        //file_put_contents($_SERVER["DOCUMENT_ROOT"] ."/apit/errors.txt",$errors,FILE_APPEND);
        //exit;
    }
    
    
    
    /**
	Frame error report method
	*/
    function error($errCode, $errDetails, $errMessage, $function, $information)
    {
        $this->out->setErrorData($errCode, $errDetails, $errMessage, $function, $information);
    }
    
	/**
	Write a text into a file
	*/
    function writeTextFile($filename, $appendText)
    {
        file_put_contents($filename, $appendText, FILE_APPEND);
    }
    
	/**
	Set the current data output in frame
	Optional set the frame status 
	*/
    function output($output, $status = "OK")
    {
        
        $this->out->data = $output;
        
        $this->out->setEndTime(microtime(true));
        $this->out->status = $status;
        $this->out->evaluate();
        echo json_encode($this->out);
        
    }
    
    
    
    /**
	Dummy Method, will be a basic init of db connection
	*/
    function openDatabaseConection($server, $password, $params = null)
    {
        
    }
    
    /**
	Main Method:
	Handling and parsing document comments and execute called Method with specific parameters. 
	The Comments are read by token_get_all() and procssed with array functions
	! Warning: Reading this code can confuse you! Ist not commented and very inunderstandable!
	*/
    function handleApiUrls($filePath, $methodUrl)
    {
        
        ob_get_clean(); // Redirect Output, so you can change header after giving an output in functions
        ob_start();
        
        $docComments = array_filter( // get Method comments
            token_get_all(file_get_contents($filePath)), function($entry)
        {
            if ($entry[0] == T_STRING)
                return true;
            if ($entry[0] == T_DOC_COMMENT)
                return true;
            if ($entry[0] == T_FUNCTION)
                return true;
            
            return false;
            
        });
        
        
        
        $docComments = array_values($docComments); //Reintegrate new ArrayKeys
        $x           = array();
        
        for ($i = 0; $i < count($docComments); $i++) {
            
            $elem = $docComments[$i];
            //print_r($elem);
            if ($elem[0] == 375 || $elem[0] ==367) //if 375 dont work try 367  Comment found, scanning elements
                {
                $arr = array(
                    $elem[1],
                    $docComments[$i + 1][1],
                    $docComments[$i + 2][1]
                );
                $x[] = $arr;
                $i++;
                
            }
            
        }
        
        $methods = array();
        
        
        foreach ($x as $meth) {
            
            $comment  = $meth[0];
            $funcName = $meth[2];
            
            $registerArray = explode("\n", $comment); // Split comment into its lines
            $parameters    = array();
            
            foreach ($registerArray as $params) {
                if (strpos($params, "@") !== false) {
                    $line = substr($params, 1);
                    
                    
                    if ($this->startsWith($line, "get")) //Line with URL Template
                        {
                        $geturl = substr($line, 4);
                        
                        
						}
                    
                    
                    if ($this->startsWith($line, "param")) //Line with a kind of param
                        {
                        $param     = substr($line, 6);
                        $arr       = explode(" ", $param);
                        // print_r($arr);
                        $paramName = $arr[0];
                        
                        foreach ($arr as $pN) {
                            if ($this->startsWith($pN, "method")) {
                                $mStr = $this->getStringBetween($pN, "\"", "\"");
                                if ($mStr == "get" && isset($_GET[$paramName])) {
                                    $parameters[$paramName][0] = $_GET[$paramName];
                                } else if ($mStr == "post" && isset($_POST[$paramName])) {
                                    $parameters[$paramName][0] = $_POST[$paramName];
                                }
                                
                            } else if ($this->startsWith($pN, "pattern")) {
                                $pt                        = $this->getStringBetween($pN, "\"", "\"");
                                $parameters[$paramName][1] = $pt;
                            }
                        }
                        
                        
                    }
                    
                    
                    
                    
                }
                
                
                
                
            }
            $regex = array();
            if ($this->debug == true)
                print_r($geturl);
            if (isset($geturl) and isset($parameters)) //if data is avalible and parameters exists
                {
                
                
                if ((strpos($geturl, '{') !== false)) // if a urltemplate has parameter in the url
                    {
                  
                    if ($this->startsWith($geturl, substr($methodUrl, 0, strpos($methodUrl, '{')))) {
                        $isRightUrl = false;
                        
                        $erg = explode("/{", $geturl);
                        
                        
                        
                        
                        foreach ($erg as $rrrr) {
                            $reg   = explode(":", $rrrr);
                            $reg[] = "";
                            {
                                $str            = $this->getStringBetween($reg[1], "\"", "\"");
                                $regex[$reg[0]] = $str;
                                
                                $repl   = ":\"" . $str . "\"";
                                $geturl = str_replace($repl, "", $geturl);
                                
                            }
                        }
                        
                        $org     = explode("/", $geturl);
                        $changed = explode("/", $methodUrl);
                        
                        if ($this->debug) {
                            
                            
                            print_r(array("org"=>$org,"changed"=>$changed,"geturl"=>$geturl,"methodUrl"=>$methodUrl));
                        }
                        
                        
						if($this->templateArrayEqual($org,$changed,$parameters))
						{
							$methods[trim($methodUrl)] = array(
                                    trim($funcName),
                                    $parameters
                                );
								
						}
						else
						{
							$methods[trim($geturl)] = array(
                                    trim($funcName),
                                    $parameters
                                );
								
						}
                      
                        
                    }
                } else {
                    
                    $methods[trim($geturl)] = array(
                        trim($funcName),
                        $parameters
                    ); //Write method and ins params into an array
                }
                unset($geturl); //delete url template
            }
            
            
            
            
            
            if ($this->debug)
                print_r($methods);
            
            
            
        }
        
        $act_meth = $methods[$methodUrl]; // get requestd method by url
        if ($this->debug)
            print_r($methods);
        //check regex
		
		//print_r($methods);
		if ($act_meth != null) {
        foreach ($act_meth[1] as $key => $value) {
            
            if ($value[1] != "" && !preg_match($value[1], $value[0])) {
                if ($this->debug)
                    echo ("\n\nError: $value does not match " . $act_meth[2][$key] . "\n\n");
                $this->error(12, "RegexError", "Regex error at Parameter \"$key\"", "handleApiUrls", "Regex error: \n Value \"$value[0]\"  does not match Pattern \"" . $value[1] . "\" ");
                $this->output(null, "Error");
                return;
            }
            ;
        }
        
        
        
            
            $sort_params = array();
            $refFunc     = new ReflectionFunction($act_meth[0]); // get Refelction of requested function
            foreach ($refFunc->getParameters() as $param) {
                
                if(isset($act_meth[1][$param->name][0]))$sort_params[] = $act_meth[1][$param->name][0]; // Sort params,that tey are in the right order, how the function expects
            }
            
            
            $sort_params[] = $this;
            
            try {
                call_user_func_array($act_meth[0], $sort_params); // call the function with parameters
            }
            catch (Exception $x) {
                $this->error($x->getCode(), $x->getMessage(), "Error", $act_meth[0], $x->getMessage() . " at " . $x->getFile() . ":" . $x->getLine() . " trace: " . $x->getTraceAsString());
                $this->output(null, "Error");
                 $this->setHttpStatusCode(500);
                
                return;
            }
            
        } else {
            $this->error(12,"Url not defined","The request url was not defined","handleApiUrls","The Url '" . $methodUrl . "' was not defined as function. Please check your definition scripts again!"); // URL isn' definded
			$this->output(null,"Error");
            $this->setHttpStatusCode(501);

        }
        
        
        
    }
    /**
	Helper functions for parsing and processing
	**/
    function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    
    
    function getStringBetween($str, $from, $to)
    {
        $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
        return substr($sub, 0, strpos($sub, $to));
    }
	
	function templateArrayEqual($templateArray,$realArray,&$parameters)
	{
		$out = false;
		if(count($templateArray)==count($realArray))
		{
			
			for($i=0;$i<count($templateArray);$i++)
			{
				
				if(trim($templateArray[$i])==trim($realArray[$i]))
				{
					$out=true;
				}
				else if($this->startsWith($templateArray[$i],"{"))
				{
					
               
               $paramName              = $this->getStringBetween($templateArray[$i], "{", "}"); //Parse param name
               $value                  = $realArray[$i];
               $parameters[$paramName] = array(
                   $value,
                   $regex[$paramName]
               );
                             
                                 
					$out= true;
				}
				else
				{
					
					$out=false;
				}
				
				if(!$out) return $out;
			}
			
			
			return $out;
			
		}
		
		return false;
	}
    
    
    /**
    Generic cast function, casts an object to generic string type
    */
    static function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection      = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties      = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name  = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
    
    /**
    Casts a unknown Frame object to a typed Frame object, which you can use and manipulate
    */
    static function get($frameObject)
    {
        return Frame::cast('Frame', (object) $frameObject);
    }
    
    
    
    
    
    
    
    
    
    
}






?>