<?php
namespace AgrandesR;

use Exception;

//Version: 1.0
class Router {
    //Request data
    protected $req_uri;
    protected $req_method;
    //Route data
    protected $route_path;
    protected $route_method;
    protected $route_req_parameters;
    protected $route_opt_parameters;
    protected $route_data;
    //Route Map data
    protected $routes;
    protected $constants;

    function __construct(string $routePath = 'routes.json') {
        $this->req_uri=$this->getURI();
        $this->req_method=$_SERVER['REQUEST_METHOD'];
        $map = json_decode(file_get_contents($routePath),true);
        $this->routes = $map['routes'];
        $this->constants = $map['constants'];
    }

    public function run() : void {
        try {
            foreach($this->routes as $path => $pathData){
                if($path==$this->req_uri){
                    $this->route_path=$path;
                    
                    $this->route_req_parameters=isset($pathData['ext']['req_parameters']) ? $pathData['ext']['req_parameters'] : [];
                    $this->route_opt_parameters=isset($pathData['ext']['opt_parameters']) ? $pathData['ext']['opt_parameters'] : [];
    
                    foreach ($pathData as $method => $methodData) {
                        if($method==$this->req_method){
                            if(isset($methodData['parameters'])){
                                $this->route_req_parameters=array_merge($this->route_req_parameters, $methodData['req_parameters']);
                                $this->route_opt_parameters=array_merge($this->route_req_parameters, $methodData['opt_parameters']);
                            }
                            $isFound=true;
                            $this->route_data=$methodData;
                            break 2;
                        }
                    }
                } 
            }
            //Execute the render option
            if($isFound){
                $this->render();
            } else {
                http_response_code(404);
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
            $content = $this->route_data['render']['content'];
            switch($this->route_data['render']['type']){
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

    private function getURI() : string {
        $uri=$_SERVER['REQUEST_URI'];
        $uri = trim($uri,'/');
        $paramsSymbolPosition = strpos($uri, '?', 0);
        if($paramsSymbolPosition>0){
            $uri=substr( $uri, 0, $paramsSymbolPosition);
        }
        return $uri;
    }
}