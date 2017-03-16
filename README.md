# CORE
Small simple PHP MVC RESTlike framework for training purposes

## MVC
Consists of two types of classes:
**Controllers** and **Views**.

The **Model** implementation depends on the particular progect design and has no base classes in the framework, yet

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
Для обработки других запросов к сайту, обработчики запросов должны быть описаны в соответствующих контроллерах. Напримеh обращения к адресам начинающимся с 
```
http://mysite.com/user
```
Должны быть описаны в контроллере UserController 

 
