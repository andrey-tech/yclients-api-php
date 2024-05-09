# YCLIENTS API PHP Wrapper

![YCLIENTS logo](./assets/yclients-logo.png)

Обертка на PHP7+ для работы с [REST API YCLIENTS v2.0](https://yclients.docs.apiary.io/) 
c троттлингом запросов к API и логированием в файл.

Данная библиотека является форком [Yclients API wrapper](https://github.com/slowprog/yclients-api) со следующими изменениями: 

- добавлен регулируемый троттлинг запросов к API;
- добавлена отключаемая проверка SSL/TLS-сертификата сервера YCLIENTS;
- добавлена проверка наличия сообщений об ошибках в ответе API;
- добавлено логирование запросов и ответов сервера в файл или STDOUT;
- изменен и дополнен тест сообщений об ошибках;
- добавлены методы getSchedule(), getGroups();
- изменен метод postHooks() в связи с изменениями в API;
- добавлен метод getAll() для выгрузки всех сущностей одного типа с использованием генератора при обработке больших объемов данных.

## Содержание

<!-- MarkdownTOC levels="1,2,3,4,5,6" autoanchor="true" autolink="true" -->

- [Требования](#%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
- [Установка](#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0)
- [Класс `YclientsApi`](#%D0%9A%D0%BB%D0%B0%D1%81%D1%81-yclientsapi)
    - [Список методов класса](#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%BC%D0%B5%D1%82%D0%BE%D0%B4%D0%BE%D0%B2-%D0%BA%D0%BB%D0%B0%D1%81%D1%81%D0%B0)
        - [Авторизация](#%D0%90%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F)
        - [Онлайн-запись](#%D0%9E%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD-%D0%B7%D0%B0%D0%BF%D0%B8%D1%81%D1%8C)
        - [Записи пользователя](#%D0%97%D0%B0%D0%BF%D0%B8%D1%81%D0%B8-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F)
        - [Сети салонов](#%D0%A1%D0%B5%D1%82%D0%B8-%D1%81%D0%B0%D0%BB%D0%BE%D0%BD%D0%BE%D0%B2)
        - [Компании](#%D0%9A%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8)
        - [Категория услуг](#%D0%9A%D0%B0%D1%82%D0%B5%D0%B3%D0%BE%D1%80%D0%B8%D1%8F-%D1%83%D1%81%D0%BB%D1%83%D0%B3)
        - [Услуги](#%D0%A3%D1%81%D0%BB%D1%83%D0%B3%D0%B8)
        - [Сотрудники](#%D0%A1%D0%BE%D1%82%D1%80%D1%83%D0%B4%D0%BD%D0%B8%D0%BA%D0%B8)
        - [Клиенты](#%D0%9A%D0%BB%D0%B8%D0%B5%D0%BD%D1%82%D1%8B)
        - [Записи](#%D0%97%D0%B0%D0%BF%D0%B8%D1%81%D0%B8)
        - [Расписание работы сотрудников](#%D0%A0%D0%B0%D1%81%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B-%D1%81%D0%BE%D1%82%D1%80%D1%83%D0%B4%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2)
        - [Даты для журнала](#%D0%94%D0%B0%D1%82%D1%8B-%D0%B4%D0%BB%D1%8F-%D0%B6%D1%83%D1%80%D0%BD%D0%B0%D0%BB%D0%B0)
        - [Комментарии](#%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%80%D0%B8%D0%B8)
        - [Пользователи компании](#%D0%9F%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D0%B8-%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8)
        - [Кассы](#%D0%9A%D0%B0%D1%81%D1%81%D1%8B)
        - [SMS рассылка](#sms-%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B0)
        - [Склады](#%D0%A1%D0%BA%D0%BB%D0%B0%D0%B4%D1%8B)
        - [Уведомления о событиях webhooks](#%D0%A3%D0%B2%D0%B5%D0%B4%D0%BE%D0%BC%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BE-%D1%81%D0%BE%D0%B1%D1%8B%D1%82%D0%B8%D1%8F%D1%85-webhooks)
        - [Вспомогательные методы](#%D0%92%D1%81%D0%BF%D0%BE%D0%BC%D0%BE%D0%B3%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D1%8B%D0%B5-%D0%BC%D0%B5%D1%82%D0%BE%D0%B4%D1%8B)
    - [Дополнительные параметры](#%D0%94%D0%BE%D0%BF%D0%BE%D0%BB%D0%BD%D0%B8%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D1%8B%D0%B5-%D0%BF%D0%B0%D1%80%D0%B0%D0%BC%D0%B5%D1%82%D1%80%D1%8B)
    - [Примеры](#%D0%9F%D1%80%D0%B8%D0%BC%D0%B5%D1%80%D1%8B)
- [Авторы](#%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B)
- [Лицензия](#%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F)

<!-- /MarkdownTOC -->

<a id="%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F"></a>
## Требования

- PHP >= 7.0.
- Произвольный автозагрузчик классов, реализующий стандарт [PSR-4](https://www.php-fig.org/psr/psr-4/).


<a id="%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0"></a>
## Установка

Установка через composer:
```
$ composer require andrey-tech/yclients-api-php
```

или добавить

```
"andrey-tech/yclients-api-php": "^1.7"
```

в секцию require файла composer.json.


<a id="%D0%9A%D0%BB%D0%B0%D1%81%D1%81-yclientsapi"></a>
## Класс `YclientsApi`

Для работы с REST API YCLIENTS используется методы класса `\Yclients\YclientsApi`.  
При возникновении ошибок выбрасывается исключение с объектом класса `\Yclients\YclientsException`.  

<a id="%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%BC%D0%B5%D1%82%D0%BE%D0%B4%D0%BE%D0%B2-%D0%BA%D0%BB%D0%B0%D1%81%D1%81%D0%B0"></a>
### Список методов класса

- `__construct(string $tokenPartner = null)` Конструктор класса.

<a id="%D0%90%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F"></a>
#### Авторизация

- `setTokenPartner(string $tokenPartner) :void` Устанавливает токен партнера.
- `getTokenPartner() :string` Возвращает токен партнера.
- `getAuth(string $login, string $password) :array` Выполняет авторизацию и возвращает токен пользователя.

<a id="%D0%9E%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD-%D0%B7%D0%B0%D0%BF%D0%B8%D1%81%D1%8C"></a>
#### Онлайн-запись

- `getBookform($id) :array` Возвращает настройки формы бронирования.
- `getI18n($locale = 'ru-RU') :array` Возвращает параметры интернационализации.
- `getBookServices($companyId, $staffId = null, \DateTime $datetime = null, array $serviceIds = null, array $eventIds = null) :array`
    Возвращает список услуг, доступных для бронирования.
- `getBookStaff($companyId, $staffId = null, \DateTime $datetime = null, array $serviceIds = null, array $eventIds = null, $withoutSeances = false) :array`
    Возвращает список сотрудников, доступных для бронирования.
- `getBookDates($companyId, $staffId = null, array $serviceIds = null, \DateTime $date = null, array $eventIds = null) :array`
    Возвращает список дат, доступных для бронирования.
- `getBookTimes($companyId, $staffId, \DateTime $date, array $serviceIds = null, array $eventIds = null) : array`
    Возвращает список сеансов, доступных для бронирования.
- `postBookCode($companyId, $phone, $fullname = null) :array` Отправляет СМС код подтверждения номера телефона.
- `postBookCheck($companyId, array $appointments): array` Проверяет параметры записи.
- `postBookRecord($companyId, array $person, array $appointments, $code = null, array $notify = null, $comment = null, $apiId = null) :array`
    Создает запись на сеанс.

<a id="%D0%97%D0%B0%D0%BF%D0%B8%D1%81%D0%B8-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F"></a>
#### Записи пользователя

- `postUserAuth($phone, $code) :array` Выполняет авторизовацию пользователя по номеру телефона и SMS-коду.
- `getUserRecords($recordId, $recordHash = null, $userToken = null): array` Возвращает записи пользователя.
- `deleteUserRecords($recordId, $recordHash = null, $userToken = null): array` Удаляет записи пользователя.

<a id="%D0%A1%D0%B5%D1%82%D0%B8-%D1%81%D0%B0%D0%BB%D0%BE%D0%BD%D0%BE%D0%B2"></a>
#### Сети салонов

- `getGroups($userToken): array` Возвращает список доступных сетей салонов

<a id="%D0%9A%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8"></a>
#### Компании

- `getCompanies($groupId = null, $active = null, $moderated = null, $forBooking = null, $my = null, $userToken = null) :array`
    Возвращает список компаний.
- `postCompany(array $fields, $userToken) :array` Создает компанию.
- `getCompany($id) :array` Возвращает компанию.
- `putCompany($id, array $fields, $userToken) :array` Изменяет компанию.
- `deleteCompany($id) :array` Удаляет компанию.
- `getCompanyAnalytics($companyId, $dateFrom, $dateTo, $userToken)` Возвращает основные показатели компании.

<a id="%D0%9A%D0%B0%D1%82%D0%B5%D0%B3%D0%BE%D1%80%D0%B8%D1%8F-%D1%83%D1%81%D0%BB%D1%83%D0%B3"></a>
#### Категория услуг

- `getServiceCategories($companyId, $categoryId = null, $staffId = null) :array` Возвращает список категорий услуг.
- `postServiceCategories($companyId, $categoryId, $fields, $userToken) :array` Создает категорию услуг.
- `getServiceCategory($companyId, $categoryId) :array` Возвращает категорию услуг.
- `putServiceCategory($companyId, $categoryId, $fields, $userToken) :array` Изменяет категорию услуг.
- `deleteServiceCategory($companyId, $categoryId, $userToken) :array` Удаляет категорию услуг.

<a id="%D0%A3%D1%81%D0%BB%D1%83%D0%B3%D0%B8"></a>
#### Услуги

- `getServices($companyId, $serviceId = null, $staffId = null, $categoryId = null) :array` Возвращает список услуг или конкретную услугу.
- `postServices($companyId, $serviceId, $categoryId, $title, $userToken, array $fields = null) :array` Создает новую услугу.
- `putServices($companyId, $serviceId, $categoryId, $title, $userToken, array $fields = null) :array` Изменяет услугу.
- `deleteServices($companyId, $serviceId, $userToken) :array` Удаляет услугу.

<a id="%D0%A1%D0%BE%D1%82%D1%80%D1%83%D0%B4%D0%BD%D0%B8%D0%BA%D0%B8"></a>
#### Сотрудники

- `getStaff($companyId, $staffId = null) :array` Возвращает список сотрудников или конкретного сотрудника.
- `postStaff($companyId, $staffId, $name, $userToken, array $fields = null) :array` Добавляет нового сотрудника.
- `putStaff($companyId, $staffId, array $fields, $userToken) :array` Изменяет сотрудника.
- `deleteStaff($companyId, $staffId, $userToken) :array` Удаляет сотрудника.

<a id="%D0%9A%D0%BB%D0%B8%D0%B5%D0%BD%D1%82%D1%8B"></a>
#### Клиенты

- `getClients($companyId, $userToken, $fullname = null, $phone = null, $email = null, $page = null, $count = null) :array`
    Возвращает список клиентов.
- `postClients($companyId, $name, $phone, $userToken, array $fields = []) :array` Добавляет клиента.
- `getClient($companyId, $id, $userToken) :array` Возвращает клиента.
- `putClient($companyId, $id, $userToken, array $fields) :array` Изменяет клиента.
- `deleteClient($companyId, $id, $userToken) :array` Удаляет клиента.

<a id="%D0%97%D0%B0%D0%BF%D0%B8%D1%81%D0%B8"></a>
#### Записи

- `getRecords($companyId, $userToken, $page = null, $count = null, $staffId = null, $clientId = null, \DateTime $startDate = null, \DateTime $endDate = null, \DateTime $cStartDate = null, \DateTime $cEndDate = null, \DateTime $changedAfter = null, \DateTime $changedBefore = null) :array`
    Возвращает список записей.
- `postRecords($companyId, $userToken, $staffId, $services, $client, \DateTime $datetime, $seanceLength, $saveIfBusy, $sendSms, $comment = null, $smsRemainHours = null, $emailRemainHours = null, $apiId = null, $attendance = null) :array`
    Создает новую запись.
- `getRecord($companyId, $recordId, $userToken) :array` Возвращает запись.
- `putRecord($companyId, $recordId, $userToken, array $fields) :array` Изменяет запись.
- `deleteRecord($companyId, $recordId, $userToken) :array` Удаляет запись.

<a id="%D0%A0%D0%B0%D1%81%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B-%D1%81%D0%BE%D1%82%D1%80%D1%83%D0%B4%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2"></a>
#### Расписание работы сотрудников

- `getSchedule($companyId, $staffId, $startDate, $endDate, $userToken) :array` Возвращает расписание работы сотрудника.
- `putSchedule($companyId, $staffId, $userToken, $fields) :array` Изменяет расписание работы сотрудника.

<a id="%D0%94%D0%B0%D1%82%D1%8B-%D0%B4%D0%BB%D1%8F-%D0%B6%D1%83%D1%80%D0%BD%D0%B0%D0%BB%D0%B0"></a>
#### Даты для журнала

- `getTimetableDates($companyId, \DateTime $date, $staffId, $userToken) :array` Возвращает список дат для журнала.
- `getTimetableSeances($companyId, \DateTime $date, $staffId, $userToken) :array` Возвращает список сеансов для журнала.

<a id="%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%80%D0%B8%D0%B8"></a>
#### Комментарии

- `getComments($companyId, $userToken, \DateTime $startDate = null, \DateTime $endDate = null, $staffId = null, $rating = null) :array`
    Возвращает комментарии.

<a id="%D0%9F%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D0%B8-%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8"></a>
#### Пользователи компании

- `getCompanyUsers($companyId, $userToken) :array` Возвращает пользователей компании.

<a id="%D0%9A%D0%B0%D1%81%D1%81%D1%8B"></a>
#### Кассы

- `getAccounts($companyId, $userToken) :array` Возвращает кассы компании.

<a id="sms-%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B0"></a>
#### SMS рассылка

- `sendSMS($companyId, $userToken, $clientIds, $text) :array` Отправляет SMS.

<a id="%D0%A1%D0%BA%D0%BB%D0%B0%D0%B4%D1%8B"></a>
#### Склады

- `getStorages($companyId, $userToken) :array` Возвращает склады компании.

<a id="%D0%A3%D0%B2%D0%B5%D0%B4%D0%BE%D0%BC%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BE-%D1%81%D0%BE%D0%B1%D1%8B%D1%82%D0%B8%D1%8F%D1%85-webhooks"></a>
#### Уведомления о событиях webhooks

- `getHooks($companyId, $userToken) :array`  Возвращает настройки уведомлений о событиях.
- `postHooks($companyId, $fields, $userToken) :array` Изменяет настройки уведомлений о событиях.

<a id="%D0%92%D1%81%D0%BF%D0%BE%D0%BC%D0%BE%D0%B3%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D1%8B%D0%B5-%D0%BC%D0%B5%D1%82%D0%BE%D0%B4%D1%8B"></a>
#### Вспомогательные методы

- `getAll($callback) :\Generator` Загружает все сущности одного типа.
    + `$callback` - анонимная функция `function(int $page, int $count) { ... }`, реализующая постраничную загрузку сущностей
       с помощью методов `getClients()` или `getRecords()`:
        * `$page` - номер загружаемой страницы;
        * `$count` - максимальное количество сущностей, загружаемых на странице.

<a id="%D0%94%D0%BE%D0%BF%D0%BE%D0%BB%D0%BD%D0%B8%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D1%8B%D0%B5-%D0%BF%D0%B0%D1%80%D0%B0%D0%BC%D0%B5%D1%82%D1%80%D1%8B"></a>
### Дополнительные параметры

Дополнительные параметры работы устанавливаются через публичные свойства объекта класса `YclientsApi`.

| Свойство                | По умолчанию   | Описание                                                                                                                                                                              |
|-------------------------|----------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `$debug`                | false          | Включает отладочный режим с сохранением запросов и ответов в лог файл или выводом в STDOUT                                                                                            |
| `$debugLogFile`         | logs/debug.log | Устанавливает лог файл отладочного режима (null - вывод в STDOUT)                                                                                                                     |
| `$throttle`             | 5              | Устанавливает максимальное число запросов к API YCLIENTS в секунду ([не более 5 запросов в секунду](https://yclients.docs.apiary.io/)), значение 0 отключает троттлинг запросов к API |
| `$verifySSLCerfificate` | true           | Включает проверку SSL/TLS-сертификата сервера YCLIENTS                                                                                                                                |
| `$SSLCertificateFile`   | 'cacert.pem'   | Устанавливает файл SSL/TLS-сертификатов X.509 корневых удостоверяющих центров (CA) в формате РЕМ (null - файл, указанный в `curl.cainfo` php.ini)                                     |
| `$curlConnectTimeout`   | 30             | Устанавливает таймаут соединения с сервером YCLIENTS, секунды                                                                                                                         |
| `$curlTimeout`          | 30             | Устанавливает таймаут обмена данными с сервером YCLIENTS, секунды                                                                                                                     |
| `$limitCount`           | 300            | Максимальное количество сушностей, загружаемых за один запрос к API в методе `getAll()`                                                                                               |

<a id="%D0%9F%D1%80%D0%B8%D0%BC%D0%B5%D1%80%D1%8B"></a>
### Примеры

```php
use Yclients\YclientsApi;

try {

    $login = 'user@example.com';
    $password = '37*%4Hd.Uda)532';
    $tokenPartner = 'erd8jrpo4mk7lsk8krs';

    $yc = new YclientsApi($tokenPartner);

    // Включаем отладочный режим с логированием в файл
    $yc->debug = true;

    // Устанавливаем лог файл отладочного режима
    $ys->debugLogFile = 'logs/debug_yclients_api.log';

    // Устанавливает максимальное число запросов к API YCLIENTS в секунду (значение 0 отключает троттлинг запросов к API)
    $yc->throttle = 1;

    // Выполняем авторизацию и получаем токен пользователя
    $response = $yc->getAuth($login, $password);
    $userToken = $response['user_token'];

    /*
     * Получаем список активных, прошедших модерацию компаний YCLINETS,
     * на управление которыми пользователь имеет права
     */
    $companies = $yc->getCompanies(
        $groupId = null,
        $active = true,
        $moderated = true,
        $forBooking = null,
        $my = 1,
        $userToken
    );

    // Получаем ID первой компании
    $companyId = $companies[0]['id'];

    // Получаем всех пользователей первой компании
    $users = $yc->getCompanyUsers($companyId, $userToken);
    print_r($users);

    // Получаем всех сотрудников компании
    $staff = $yc->getStaff($companyId);
    print_r($staff);

    // Получаем ID первого сотрудника
    $workerId = $staff[0]['id'];

    // Загружаем расписание работы первого сотрудника на 1 месяц
    $schedule = $yc->getSchedule(
        $companyId,
        $workerId,
        $startDate = '2020-01-01',
        $endDate = '2020-01-31'
        $userToken
    );
    print_r($schedule);

    /**
     * Выгружаем всех клиентов заданной компании с использованием генератора 
     * при обработке больших объемов данных
     */
    $generator = $yc->getAll(
        function (int $page, int $count) use ($yc, $companyId, $userToken) {
            return $yc->getClients(
                $companyId,
                $userToken,
                $fullname = null,
                $phone = null,
                $email = null,
                $page,
                $count
            );
        }
    );
    foreach ($generator as $response) {
        $clients = $response['data'];
        foreach ($clients as $client) {
            print_r($client);
        }
    }

    /**
     * Выгружаем все записи сотрудника заданной компании с использованием генератора 
     * при обработке больших объемов данных
     */
    $generator = $yc->getAll(
        function (int $page, int $count) use ($yc, $companyId, $userToken, $workerId) {
            return $yc->getRecords(
                $companyId,
                $userToken,
                $page,
                $count,
                $workerId
            );
        }
    );
    foreach ($generator as $response) {
        $records = $response['data'];
        foreach ($records as $record) {
            print_r($record);
        }
    }

} catch (\Yclients\YclientsException $e) {
    printf('Ошибка (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
```

<a id="%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B"></a>
## Авторы

© 2018 slowprog  
© 2019-2024 andrey-tech

<a id="%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F"></a>
## Лицензия

Данная библиотека распространяется на условиях лицензии [MIT](./LICENSE).
