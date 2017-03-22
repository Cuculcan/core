# CORE
Small simple PHP MVC RESTlike framework for training purposes

## MVC
Consists of two types of classes:
**Controllers** and **Views**.

The **Model** implementation depends on the particular progect design and has no base classes in the framework, yet

### Контроллеры
Контроллеры предназначены для обработки входящих запросов

Любой запрос к сайту проходит через .htaccess и направляется в  index.php  

Условная последовательность обработки запроса следующая:
```
.htaccess->index.php->Request->Router->Controller
```

Цепочка наследования контроллеров
```
AController
    ^
    |
BaseController
    ^
    |
MainController и другие контроллеры
```

**AController** является абстрактным. Вызывает методы обработки адресов описанных в конкретных контроллерах. Не должен зависеть от конкретного проекта

**BaseController** наследуется от AController и содержит общие методы для всех пользовательский контроллеров. Например инициализация сессионного пользователя.
 Подготовка данных для меню и другие.

***MainController и другие пользовательские контроллеры*** наследуются от BaseController и содержат методы для обработки конкретных url адресов.
В проекте должен быть как минимум один контроллер с именем **MainController**, он отвечает за обработку обращений к корню сайта.
```
http://mysite.com
```
Для обработки других запросов к сайту, обработчики запросов должны быть описаны в соответствующих контроллерах. Например обращения к адресам начинающимся с 
```
http://mysite.com/user
```
Должны быть описаны в контроллере UserController 

### Работа с маршрутами
В каждом классе контроллера должна быть реализация метода 
```
protected function setActions()
```
Внутри прописываются обработчики шаблонов адресов. Существуют обработчики для ***GET, POST, PUT, DELETE*** запросов. Реализованны ввиде методов
с соответствующими названиями
```
get()
post()
del()
put()
```
Обработчик шаблона адреса имеет формат
```
$this->get|post|del|put('[шаблон адреса]', функция callback c параметром ($urlParams));
```    
Реализация реакции на запрос описывается в колбэке данного метода. 
В шаблоне адреса может быть переменный параметр который записывается в фигурные скобки и может быть доступен внутри колбэка
```
 $this->get('/user/{userId}/get_name', function ($urlParams)  {
     echo $urlParams['userId']; 
});
```
в данном примере обработается запрос вида  ***http://myexample.com/user/12345/get_name*** и будеn выведено 12345

Параметры запросов могут быть доступны через объект request и его методы 

getQueryParameters() - вернет массив параметров

getQueryParameter($name, $default=null) - вернет значение параметра или значение переданное в default если параметр отсутствует
```
 //process GET request http://myexample.com/user/set_name?name=VasiliyPupkin
$this->get('/user/set_name', function ($urlParams)  {

   $name =$this->request->getQueryParameter("name", "");           
   echo $name
});
```

Все методы описанные в setActions() выполняются поочереди и выбирается тот из них, который соответствует шаблону,
если шаблону соответствует больше одного метода выбрасывается исключение

Во всех объектах контроллера присутствует переменная ***model***.  
Она используется для передачи данных в объекты класса View для последующего отображния

Вызов объекта View осуществляется методом $this->showView("UserView") параметром является имя класса для отображения
```
//process GET request http://myexample.com/user
$this->get('/user', function ($urlParams)  {

   $this->model['some_parameter'] = 'param from user controller';
   $this->showView("UserView");
});
```
Будет отображена View c классом UserView




