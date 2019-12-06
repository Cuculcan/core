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

### Работа с представлениями 

Все представления **View** являются классами наследниками абрстрактного класса AView 
В представлениях должны быть реализованы следующие абстрактные методы родительского класса

```
abstract protected function setTemplateName();
```
Устанавливает имя шаблона который отправляется пользователю

```
abstract protected function setAdditionalCSS();
```
Устанавливает массив подключаемых CSS файлов

```
abstract protected function setAdditionalJS();
```
Устанавливает массив подключаемых javascript файлов

```
abstract protected function setCustomHeaders();
```
Устанавливает массив дополнительных заголовков

```
abstract protected function setTitle();
```
Устанавливает свойство title страницы

Так же существуют дополнительные свойства установка которых не обязательна но может понадобится для поисковой оптимизации
```
protected function setKeywords(){}

protected function setDescription(){}

protected function setLanguage(){}
```

Пример реализации класса View для главной страницы
```
<?php

include_once 'core/AView.php';

class MainView extends AView
{
    //Стандартный конструктор будет одинаков для всех View
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName()
    {
        $this->templateName = '/views/templates/main.php'; //путь к шаблону главной страницы
    }

    protected function setTitle(){
        $this->title = "Главная страница";
    }

    protected function setKeywords()    {
        $this->keywords="Главная, Страница, Другие, Ключевые, СЛова, Через Запятую";
    }

    protected function setDescription(){
        $this->description="Это главная страница сайта и еще какое-то дополнительное описание необходимое для SEO";
    }

    protected function setAdditionalCSS(){
        array_push($this->additionalCSS, '/public/css/carousel/owl.carousel.css');
        array_push($this->additionalCSS, '/public/css/carousel/owl.theme.default.min.css');
        array_push($this->additionalCSS, '/public/css/main.css');

    }

    protected function setAdditionalJS(){
        array_push($this->additionalJS, '/public/js/carousel/owl.carousel.js');
        array_push($this->additionalJS, '/public/js/main-page.js');
    }

    protected function setCustomHeaders(){
        array_push($this->customHeaders, "MY-PEW-HEADER: pew-pew-pew");
    }

}
```

Вызов View происходит внутри обработчика запроса в контроллере
```
   $this->model['some_parameter'] = 'param from user controller';
   $this->showView("UserView", $this->model);
```
параметрами являеются: первый - имя класса  представления, второй - модель данных для заполнения шаблона

### Шаблоны

Шаблонами являются произвольные PHP файлы (дополнительные шаблонизаторы не используются)
Шаблоны должны храниться в папке templates. Могут включать директивы include любой вложенности
В движке испольуется разбиение на header, content, и footer
Пример шаблона для заголовка
```
<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title>
        <?php
        if ($this->title) {
            echo $this->title;
        }
        ?>
    </title>
    <meta name="keywords" content="<?php
    if ($this->keywords) {
        echo $this->keywords;
    }
    ?>" />
    <meta name="description" content="<?php
    if ($this->description) {
        echo $this->description;
    }
    ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="author" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    
    <!--глобальные стили-->
    <link href="/css/layout.css" rel="stylesheet" type="text/css" />
    <!--дополнительные стили-->
    <?php
    if ($this->additionalCSS) {
        foreach ($this->additionalCSS as $css) {
            echo '<link rel="stylesheet" href="' . $css . '">' . "\r\n    ";
        }
    }
    ?>

    <!--глобальные скрипты-->
    <script src="/js/jquery.min.js" type="text/javascript"></script>
    <!--дополнительные скрипты-->
    <?php
    if ($this->additionalJS) {
        foreach ($this->additionalJS as $js) {
            echo '<script src="' . $js . '"></script>' . "\r\n    ";
        }
    }
    ?>

    
</head>
<!-- END HEAD -->

<body>
```   

Таким образом главная страница в простейшем варианте может выглядеть
```
<?php include '../src/Example/views/templates/header.php' ?>

<h1> Main page here!! </h1>
<h3>
   <?php echo $this->model['languege']; ?>
</h3>

<?php include '../src/Example/views/templates/footer.php' ?>

```

Доступ к данным модели внутри шаблона осуществляется через обращение к **$this->model**



