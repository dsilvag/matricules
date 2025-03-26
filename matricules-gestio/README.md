<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Dades connexió web service ADM

# Endpoints Portal Extern #ADM 2.0

## Password Grant (Login)

**METHOD:** POST

```
http://3.68.148.3:8080/alpha-data-manager/oauth/token?grant_type=password&username=usuarix@alphanet.cat&password=contrasenyadelusuari
```

#### **Authorization**

Username & Password



#### **Query Params**

**grant_type:** password

**username:** usuarix@alphanet.cat

**password:** contrasenyadelusuari



#### **Example**:

```php
<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('3.68.148.3:8080/alpha-data-manager/oauth/token?grant_type=password&username=usuarix@alphanet.cat&password=contrasenyadelusuari');
$request->setMethod(HTTP_Request2::METHOD_POST);
$request->setConfig(array(
  'follow_redirects' => TRUE
));
$request->setHeader(array(
  'Authorization' => 'Basic ZnJvbnRlbmQtdHJ1c3RlZC1jbGllbnQ6Q0FrdVRxYWx1OXliWks3dXRnSA=='
));
try {
  $response = $request->send();
  if ($response->getStatus() == 200) {
    echo $response->getBody();
  }
  else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    $response->getReasonPhrase();
  }
}
catch(HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
```



#### Response:

```json
{
    "access_token": "25sCQKCJLPJTHLzNhY1IDblTg5c",
    "token_type": "bearer",
    "refresh_token": "7MnccmCJ8DpqBJ-F6g7o8pQP_JM",
    "expires_in": 43199,
    "scope": "all",
    "authorities": [],
    "tenantAcceptedAllLegalDisclaimers": "true",
    "tenantType": "TOWN_HALL",
    "shieldActive": "false",
    "language": "CA",
    "timeZone": "Europe/Madrid",
    "userEmail": "usuarix@alphanet.cat",
    "distributorName": "AlphanetSolutions",
    "permissions": [
        "GENERAL131"
    ],
    "tenantId": 417,
    "groupId": 562,
    "distributorId": 1,
    "tenantName": "Pals",
    "groupName": "Usuaris externs - Autoritzats_CA_Llista",
    "userFirstName": "nom-usuari"
}
```



---



## Refresh Token

**METHOD:** POST

```
http://3.68.148.3:8080/alpha-data-manager/oauth/token
```

#### **Authorization**

Username & Password



#### **Query Params**

**grant_type:** password

**username:** usuarix@alphanet.cat

**password:** contrasenyadelusuari



#### **Example**:

```php
<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('3.68.148.3:8080/alpha-data-manager/oauth/token');
$request->setMethod(HTTP_Request2::METHOD_POST);
$request->setConfig(array(
  'follow_redirects' => TRUE
));
$request->setHeader(array(
  'Content-Type' => 'application/x-www-form-urlencoded',
  'Authorization' => 'Basic ZnJvbnRlbmQtdHJ1c3RlZC1jbGllbnQ6Q0FrdVRxYWx1OXliWks3dXRnSA=='
));
$request->addPostParameter(array(
  'grant_type' => 'refresh_token',
  'refresh_token' => 'hjG9H88RYVv2kGjTANjopgfl9Lg'
));
try {
  $response = $request->send();
  if ($response->getStatus() == 200) {
    echo $response->getBody();
  }
  else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    $response->getReasonPhrase();
  }
}
catch(HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
```



#### Response:

```json
{
    "access_token": "25sCQKCJLPJTHLzNhY1IDblTg5c",
    "token_type": "bearer",
    "refresh_token": "7MnccmCJ8DpqBJ-F6g7o8pQP_JM",
    "expires_in": 43199,
    "scope": "all",
    "authorities": [],
    "tenantAcceptedAllLegalDisclaimers": "true",
    "tenantType": "TOWN_HALL",
    "shieldActive": "false",
    "language": "CA",
    "timeZone": "Europe/Madrid",
    "userEmail": "usuarix@alphanet.cat",
    "distributorName": "AlphanetSolutions",
    "permissions": [
        "GENERAL131"
    ],
    "tenantId": 417,
    "groupId": 562,
    "distributorId": 1,
    "tenantName": "Pals",
    "groupName": "Usuaris externs - Autoritzats_CA_Llista",
    "userFirstName": "nom-usuari"
}
```





---



## Create Authorized List Plate (Crear una Matrícula)

**METHOD:** POST

```
3.68.148.3:8080/alpha-data-manager/api/1.0/portal
```

#### **Request Headers**

Authorization: {{access_token}}



#### **Body**

```json
{
    "plate": "TEST12345",
    "startsOn": "2022-02-01",
    "expiresOn": "2022-02-02"
}
```



#### **Example**:

```php
<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('3.68.148.3:8080/alpha-data-manager/api/1.0/portal');
$request->setMethod(HTTP_Request2::METHOD_POST);
$request->setConfig(array(
  'follow_redirects' => TRUE
));
$request->setHeader(array(
  'Authorization' => 'Bearer iIxCyda3rMwV2rxlLQSj7pcQvXI',
  'Content-Type' => 'application/json'
));
$request->setBody('{\n    "plate": "TEST89345",\n    "startsOn": "2023-07-01",\n    "expiresOn": "2023-08-01"\n}');
try {
  $response = $request->send();
  if ($response->getStatus() == 200) {
    echo $response->getBody();
  }
  else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    $response->getReasonPhrase();
  }
}
catch(HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
```



#### Response

```json
{
    "id": 172119
}
```



---



## Delete Authorized List Plate (Eliminar una Matrícula)

**METHOD:** DELETE

```
3.68.148.3:8080/alpha-data-manager/api/1.0/portal/172117
```

#### **Request Headers**

Authorization: {{access_token}}



#### **Example**:

```php
<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('3.68.148.3:8080/alpha-data-manager/api/1.0/portal/172117');
$request->setMethod(HTTP_Request2::METHOD_DELETE);
$request->setConfig(array(
  'follow_redirects' => TRUE
));
$request->setHeader(array(
  'Authorization' => 'Bearer iIxCyda3rMwV2rxlLQSj7pcQvXI'
));
try {
  $response = $request->send();
  if ($response->getStatus() == 200) {
    echo $response->getBody();
  }
  else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    $response->getReasonPhrase();
  }
}
catch(HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
```



#### Response

```json
{
    "value": true
}
```



---



## Update Authorized List Plate (Actualitzar una Matrícula)

**METHOD:** PUT

```
3.68.148.3:8080/alpha-data-manager/api/1.0/portal/172117
```

#### **Request Headers**

Authorization: {{access_token}}



#### **Params** (amb els canvis)

```json
{
    "plate": "TEST1237745",
    "startsOn": "2022-02-01",
    "expiresOn": "2022-02-02"
}
```



#### **Example**:

```php
<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('3.68.148.3:8080/alpha-data-manager/api/1.0/portal/172117');
$request->setMethod(HTTP_Request2::METHOD_PUT);
$request->setConfig(array(
  'follow_redirects' => TRUE
));
$request->setHeader(array(
  'Authorization' => 'Bearer iIxCyda2rMwV2rxlLQSj7pcQvXI',
  'Content-Type' => 'application/json'
));
$request->setBody('{\n    "plate": "TEST1237745",\n    "startsOn": "2022-02-01",\n    "expiresOn": "2022-02-02"\n}');
try {
  $response = $request->send();
  if ($response->getStatus() == 200) {
    echo $response->getBody();
  }
  else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    $response->getReasonPhrase();
  }
}
catch(HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
```



#### Response

```json
{
    "value": true
}
```



---



## Find All Authorized List Plates (Llistar totes les matrícules amb paginació)

**METHOD:** GET

```
3.68.148.3:8080/alpha-data-manager/api/1.0/portal?page=0&size=10&sort=updatedOn,asc
```

#### **Request Headers**

Authorization: {{access_token}}



#### **Query Params**

**page:** 0

**size:** 10

**sort:** updatedOn,asc



#### **Example**:

```php
<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('3.68.148.3:8080/alpha-data-manager/api/1.0/portal?page=0&size=10&sort=updatedOn,asc&plates=&authorizedListIds=');
$request->setMethod(HTTP_Request2::METHOD_GET);
$request->setConfig(array(
  'follow_redirects' => TRUE
));
$request->setHeader(array(
  'Authorization' => 'Bearer iIxCyda2rMwV2rxlLQSj7pcQvXI'
));
try {
  $response = $request->send();
  if ($response->getStatus() == 200) {
    echo $response->getBody();
  }
  else {
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
    $response->getReasonPhrase();
  }
}
catch(HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
```



#### Response

```json
{
    "data": [
        {
            "id": 172117,
            "plate": "TEST12345",
            "comments": "",
            "authorizedListId": 89,
            "authorizedListName": "Autoritzats_CA_Llista",
            "startsOn": "2023-07-01",
            "expiresOn": "2023-08-01"
        },
        {
            "id": 172118,
            "plate": "TEST15345",
            "comments": "",
            "authorizedListId": 89,
            "authorizedListName": "Autoritzats_CA_Llista",
            "startsOn": "2023-07-01",
            "expiresOn": "2023-08-01"
        },
        {
            "id": 172119,
            "plate": "TEST89345",
            "comments": "",
            "authorizedListId": 89,
            "authorizedListName": "Autoritzats_CA_Llista",
            "startsOn": "2023-07-01",
            "expiresOn": "2023-08-01"
        }
    ],
    "page": 0,
    "totalPages": 1,
    "totalElements": 3,
    "numberOfElements": 3,
    "size": 10,
    "first": true,
    "last": true,
    "sort": [
        {
            "property": "updatedOn",
            "direction": "ASC"
        }
    ]
}
```



---



## Observacions:

Tenir en compte que els token d'accés triguen 12h en caducar, però es poden anar demanant tokens sense haver de tornar a fer login si es fa un refresh d'aquest (el refresh token dura 30 dies).


#adm #adm-backend #java #api #postman
