# CORE

Small simple PHP MVC RESTlike framework for training purposes

## installation

```sh
composer create-project cuculcan/core
```

## Navigation

[Model](#Model)

[Controller](#controllers)

[View](#views)

### Model

Models are objects inherited from the Entity class.
The entity corresponds to the row in the database table

each entry has at least an "id" field

User class example:

```php
class User extends Entity {

    public $name;
    public $email;
    public $cityId;
    public $login;
    public $password;
    
    /**
     * @type = json
     */
    public $subscription;
    
    /**
     * @extra
     */
    public $city_name;

    /**
     * @extra
     */
    public $region_name;

    public function __construct(array $data = []) {
        parent::__construct($data);
    }

}
```

Entity fields can be annotated.

***extra*** means that this property has no relation to the table column

***type = json***  used to pack arrays in json when saving to db

***default_value =***  used to set the default value in case the value is undefined

the constructor should be described as in the example and have at least the data parameter used to build the object after selecting from the database

### Storages

For entities, storage objects are created that implement the functionality for working with the Database
Repositories inherit from the MysqlStorage base class

СRUD methods for the Entity object are implemented in the base class MysqlStorage

```php
class UsersMysqlStorage extends MysqlStorage {

    /**
     *
     * @param \Pdo $connection
     */
    public function __construct($connection) {
        parent::__construct($connection, 'users'); //Sets the name of the table with which the storage works
    }

    /**
     * uses the parent class method getById
     * @param int $id
     * @return User
     */
    public function getById($id) {
        $data = parent::getById($id);

        if (!$data || $data === null) {
            return null;
        }

        return new User($data);
    }

     /**
     * direct query can be implemented using \PDO
     * @return int
     */
    public function countUsers() {
        $sql = " SELECT count(id) AS cnt FROM users ";

        $stm = $this->connection->prepare($sql);
        $stm->execute();
        $cnt = 0;
        while ($row = $stm->fetch(\PDO::FETCH_ASSOC)) {
            $cnt = $row['cnt'];
        }
        return $cnt;
    }
}
```

## Controllers

Controllers are designed to handle incoming requests

Any request to the site is converted using .htaccess RewriteRule

```htaccess
RewriteRule ^(.*)$ index.php?handler=$1 [QSA,L]
```

or nginx rewrite

```nginx
location @rewrite {
    rewrite ^/(.*)$ /index.php?handler=/$1;
}
```

and sent to index.php

The sequence of request processing is as follows:

```o
.htaccess->index.php->Request->Router->Controller
```

Controller Inheritance Chain

```o
AController
    ^
    |
BaseController
    ^
    |
MainController and other controllers
```

**AController** is abstract. Invokes methods for handling addresses described in specific controllers. Should not depend on a specific project

**BaseController** inherits from AController and contains common methods for all user controllers. For example, initialization of a session user.
 Preparing data for menus and others.

***MainController and other user controllers*** are inherited from BaseController and contain methods for processing specific url addresses.
The project must have at least one controller named **MainController**, it is responsible for processing calls to the root of the site.

```o
http://mysite.com
```

To process other requests to the site, request handlers must be described in the appropriate controllers. For example, addresses starting with:

```o
http://mysite.com/user
```

Must be described in UserController

### Routing

Each controller class must have a method implementation

```php
protected function setActions()
```

Handlers of URL templates are registered inside. There are handlers for ***GET, POST, PUT, DELETE*** requests. Implemented as Methods with appropriate names

```php
get()
post()
del()
put()
```

The URL template handler has the format

```php
$this->get | post | del | put ('[url pattern]', callback function with parameter ($urlParams));
```

The implementation of the response to the request is described in the callback of this method.
In the URL template, there may be a variable parameter which is written in curly brackets and may be available inside the callback

```php
$this->get('/user/{userId}/get_name', function ($urlParams)  {
    echo $urlParams['userId'];
});
```

In this example, a request of the form ***http: //myexample.com/user/12345/get_name*** will be processed and ***12345*** will be displayed

**Request parameters** can be accessed through the request object and it methods

getQueryParameters() - will return an array of parameters

getQueryParameter($name, $default = null) - will return the value of the parameter or the value passed to default, if the parameter is missing

```php
 //process GET request http://myexample.com/user/set_name?name=VasiliyPupkin
$this->get('/user/set_name', function ($urlParams)  {

   $name =$this->request->getQueryParameter("name", "");
   echo $name
});
```

All methods described in setActions() are executed in turn and the one that matches the template is selected,
if more than one method matches the pattern, an exception is thrown

In all objects of the controller, the variable ***model*** is present.
It is used to transfer data to objects of the View class for later display.

The View object is called by the method $ this->showView("UserView"). The parameter is the name of the class to display.

```php
//process GET request http://myexample.com/user
$this->get('/user', function ($urlParams)  {

   $this->model['some_parameter'] = 'param from user controller';
   $this->showView("UserView");
});
```

View will be displayed with the class UserView

## View

View classes inherit from the abstract class AView, used to display templates. PHP itself is used as a template engine.

MainView class example:

```php
class MainView extends AView
{
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName()
    {
        /*
        * задается имя шаблона относительно пути указанному в config/config.php 
        */
        $this->templateName = '/main.php';
    }

    //--------------------------------- SEO --------------------------------
    protected function setTitle(){
        $this->title = "";
    }

    protected function setKeywords()    {
        $this->keywords="";
    }

    protected function setDescription(){
        $this->description="";
    }
    //---------------------------------------------------------------------------
    protected function setAdditionalCSS(){
        array_push($this->additionalCSS, '/css/main.css');

    }

    protected function setAdditionalJS(){
        array_push($this->additionalJS, '/js/main.js');
    }

    protected function setCustomHeaders(){
        array_push($this->customHeaders, "MY-PEW-HEADER: pew-pew-pew");
    }

}
```
