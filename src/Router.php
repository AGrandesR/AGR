<?php
namespace AgrandesR;

use Exception;
use Error;

//Version: 1.0
class Router {
    //Request data
    protected string $req_uri;
    protected string $req_method;
    protected array $req_sections;
    //Route data
    protected string $route_path;
    protected string $route_method;
    protected array $route_req_parameters;
    protected array $route_opt_parameters;
    protected array $route_data;
    //Route Map data
    protected $routes;
    protected $constants;

    //AGR options
    protected bool $checkParams = true;
    protected bool $extraFiles = true;

    protected $validMethods=['GET','POST','UPDATE','DELETE'];

    function __construct(string $routePath = 'routes.json') {
        $this->req_uri=$this->getURI();
        $this->req_sections=explode('/',$this->req_uri);
        $this->req_method=$_SERVER['REQUEST_METHOD'];
        $map = json_decode(file_get_contents($routePath),true);
        $this->routes = $map['routes'];
        $this->constants = isset($map['constants'])?$map['constants']:[];
    }

    public function run() : void {
        try {
            $isFound=false;
            foreach($this->routes as $path => $pathData){
                if($path==$this->req_uri){
                    $this->route_path=$path;
                    
                    $this->route_req_parameters=$pathData['ext']['req_parameters'] ?? [];
                    $this->route_opt_parameters=$pathData['ext']['opt_parameters'] ?? [];
    
                    foreach ($pathData as $method => $methodData) {
                        if($method==$this->req_method){
                            if(isset($methodData['parameters'])){
                                $this->route_req_parameters=array_merge($this->route_req_parameters, $methodData['req_parameters']);
                                $this->route_opt_parameters=array_merge($this->route_req_parameters, $methodData['opt_parameters']);
                            }
                            $isFound=true;
                            $this->route_data=$methodData;
                            break 2;
                        } else {
                            
                        }
                    }
                } 
            }
            //Execute the render option
            if($isFound){
                //Evaluamos los parámetros!!!
                $err=$this->checkParameters($this->route_data['req_parameters']);
                if(empty($err)) $this->render();
                else $this->errorMessage($err);
            } else {
                $this->pageNotFound();
            }
        } catch(Error | Exception $e){
            echo $e->getMessage();
            die;
        }
    }

    protected function render() : void {
        //print_r($this->route_data);die;
        if(isset($this->route_data['render'])) {
            $type = $this->route_data['render']['type'];
            $content = isset($this->route_data['render']['content'])?$this->route_data['render']['content'] : null;
            switch($type){
                case "json":
                    header('Content-Type: application/json');
                    if (is_array($content)) $content=json_encode($content, JSON_PRETTY_PRINT);
                case "string":
                    echo $content;
                    die;
                case "class":
                    $path = $content['path'] . '\\' . $content['name'];
                    $func = $content['function'];
                    $class= new $path();
                    $class->$func();
                    break;
                case "doc":
                case "docs":
                    //We need to create a doc with all the routes and subroutes, etc and send to showDocumentation
                    $routeMap = json_decode(file_get_contents('routes.json'),true);
                    $this->showDocumentation($routeMap);
                    

            }
        } else {
            throw new Exception("Not render method for this path", 1);
        }
        //Load * headers
        
    }

    public function setRoutes(array $newRoutes) : bool {
        if(true) $this->routes=$newRoutes;
        else return false;
        return true;
    }

    public function addPathRoutes(string $path, array $newRoutes) : bool {
        $check=Count($this->routes);

        foreach($newRoutes as $key=>$value) {
            
            $newkey = stripslashes(str_replace('.json','',$path) ."/". $key);
            // echo $newkey . "\n";
            $this->routes[$newkey] = $value;
            //unset($arr[$oldkey]);
        }
        // die;

        return Count($this->routes)>$check;
    }

    ////////////////////////////////////////////////////////
    // S> EXTENSIBLE FUNCTIONS
    protected function pageNotFound() {
        http_response_code(404);
    }
    protected function showDocumentation(array $routeMap) {
        //In that place you can overwritte the standard model of documentation for your own style
        
    }
    protected function errorMessage(array $errorData) {
        header('Content-Type: application/json');
        //In that place you can overwritte the standard model of response in error situations
        echo json_encode([
            'status'=>false,
            'meta'=>[
                'errors'=>$errorData
            ]
        ],JSON_PRETTY_PRINT);
        die;
    }

    protected function checkParameters(array $requiredParameters=[]) : array {
        //$requiredParameters = $this->route_data['req_parameters'];
        if(empty($requiredParameters)) return true;
        $idx=0;
        $requiredErrors=[];
        foreach($requiredParameters as $parameterData){
            if(is_array($parameterData)){
                if(!isset($parameterData['name'])) $requiredErrors['ROUTER-'.++$idx]='Need to declare "name" of the parameter to can check: ' . json_encode($parameterData);
                if(!isset($_GET[$parameterData['name']])) $requiredErrors[$parameterData['name']]='Forgot required parameter';
                if(isset($parameterData['regex']) && preg_match($parameterData['regex'],$_GET[$parameterData['name']])) $requiredErrors[$parameterData['name']]='Value not valid with the regex';
            } elseif(is_string($parameterData)){
                if(!isset($_GET[$parameterData])) $requiredErrors[$parameterData]='Forgot required parameter';
            }
            //In the router PRO we put into Request method the value with the correct type ;)
        }
        return $requiredErrors;
    }
    // E> EXTENSIBLE FUNCTIONS
    ////////////////////////////////////////////////////////

    //@PRIVATE!!!
    public function getURI() : string {
        $uri=$_SERVER['REQUEST_URI'];
        $uri = trim($uri,'/');
        $paramsSymbolPosition = strpos($uri, '?', 0);
        if($paramsSymbolPosition>0){
            $uri=substr( $uri, 0, $paramsSymbolPosition);
        }
        return $uri;
    }
}