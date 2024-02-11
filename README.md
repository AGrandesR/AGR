# AGR: api-generator-router

A.G.R is a composer library designed and designed to create APIs in PHP in a fast and efficient way. It does not have the idea of being a great framework at the height of symfony and laravel but rather something light and that whoever uses it can inspect and understand. The idea is to work the entire path system from a single json file.



## Prerequisites 📋

You will need to have installed composer in your computer.

## Instalation 🔧

You need to require the package to your project.

``` bash
composer require agrandesr/agrouter
```

Next, you can use in your code. We encourage to use in the root file index.php. It is important to write under the auoload require.

``` php
<?php

require './vendor/autoload.php';

use AgrandesR\Router;

$router = new Router();

$router->run();

```

Finally, you need to create a routes.json file to start to create your API.

There is a easy example to test!

``` json
{
    "routes": {
        "hi": {
            "POST": {
                "render":{
                    "type":"json",
                    "content":"{\"test\":\"test with string\"}"
                }
            },
            "GET": {
                "render":{
                    "type":"json",
                    "content":{
                        "test":"test with object"
                    }
                }
            }
        }
    }
}
```


## How to start 🚀

The idea of the A.G.R is to make it easier to develop RESTFULL APIS and also their documentation. The first step as you have seen above is to define your API in the routes.json file.

Our concept idea was to first create the json with the default answers and then replace it with the final code. In both cases what you must always modify is the render object in **routes.PATH.METHOD.render**.

###  **JSON response**

In the first case, when you need to create a simple json response it is very easy. You need to indicate the **type** as **json** and insert the json in the content.

You can insert the json in content like a string:
``` json
{
"render":{
    "type":"json",
    "content":"{\"test\":\"test with string\"}"
}

```
Or you can insert the json in content like a object:
``` json
{
"render":{
    "type":"json",
    "content":{
        "test":"test with object"
    }
}
```

###  **Class response**
This is probably the really important render that you want. It is easy. You need to indicate the **type** as **class** and add the next object to value **content**.
``` json
{
    "path":"App\\internal",
    "name":"Test",
    "function":"json"
}
```
The values of the object are:
* **path**: The namespace of the class that you want to call
* **name**: The class name.
* **function**: The function that you want to call.

#### Example:
_render object:_
``` json
{
"render":{
    "type":"class",
    "content":{
        "path":"App\\internal",
        "name":"Test",
        "function":"json"
    }
}
```
_php class (in App\internal namespace)_
``` php
<?php
namespace App\internal;

class Test {
    public function json() {
        header('Content-Type: application/json');
        echo json_encode(["test"=>"This is only a test"]);
    }
}
```

## Required parameters
One of the most annoying things is to have to validated parameters. With Router you can do quickly adding req_parameters as a method option!

``` json
{
    "routes": {
        "hi": {
            "GET": {
                "req_parameters":[
                    "id",
                    "product",
                    {
                        "name":"email",
                        "regex":"/\w{1,}\@\w{1,}\.{2,5}/"
                    }
                ]
                "render":{
                    "type":"json",
                    "content":{
                        "test":"test with object"
                    }
                }
            }
        }
    }
}
```
Like you can look the required parameters can be the string of the parameter name o an object in which you can point the name and the regex that have to validate the parameter value.
-----
EXTRA
-----
-----

<!--
## Deployment 📦 
_Agrega additional notes on how to make deploy_ 


## Built with 🛠️ 
_Menciona the tools you used to create your proyecto_ 
* [Dropwizard](http://www.dropwizard.io/1.0.2/docs/) - The web framework used * [Maven](https://maven.apache.org/) - Dependency Manager 
* [ROME](https://rometools.github.io/rome/) - Used to generate RSS ## 

Contributing 🖇️ 
Please read [CONTRIBUTING.md](https://gist.github.com/villanuevand/xxxxxx) for details of our code of conduct, and the process for sending us pull requests. 

## Wiki 📖 
You can find much more about how to use this project in our [Wiki](https://github.com/tu/proyecto/wiki)
-->
## Versioning: 📌

We use [SemVer](http://semver.org/) for versioning. For all available versions, see the [tags in this repository](https://github.com/AGrandesR/AGR/tags).

## Autores ✒️

_Menciona a todos aquellos que ayudaron a levantar el proyecto desde sus inicios_

* **A.Grandes.R** - *Main worker* - [AGrandesR](https://github.com/AGrandesR)

You can also look at the list of all [contributors] (https://github.com/your/project/contributors) who have participated in this project.

## License 📄

This project is under the License MIT - read the file [LICENSE.md](LICENSE.md) for more details.

## Thanks to: 🎁

* [Villanuevand](https://github.com/Villanuevand) for his incredible [template](https://gist.github.com/Villanuevand/6386899f70346d4580c723232524d35a) for documentation 😊
