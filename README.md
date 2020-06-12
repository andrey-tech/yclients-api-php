# YCLIENTS API PHP Wrapper

![YCLIENTS logo](./assets/yclients-logo.png)

Обертка на PHP7+ для работы с [REST API YCLIENTS v2.0](https://yclients.docs.apiary.io/) 
c троттлингом запросов к API и логированием в файл.

Данная библиотека является форком от [Yclients API wrapper](https://github.com/slowprog/yclients-api) со следующими изменениями: 

- добавлен регулируемый троттлинг запросов к API;
- добавлена отключаемая проверка SSL/TLS-сертификата сервера YCLIENTS;
- добавлена проверка наличия сообщений об ошибках в ответе API;
- добавлено логирование запросов и ответов сервера в файл или STDOUT;
- изменен и дополнен тест сообщений об ошибках;
- добавлены методы getSchedule(), getGroups();
- изменен метод postHooks() в связи с изменениями в API.

**Документация находится в процессе разработки**

## Содержание

<!-- MarkdownTOC levels="1,2,3,4,5,6" autoanchor="true" autolink="true" -->

- [Авторы](#%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B)
- [Лицензия](#%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F)

<!-- /MarkdownTOC -->

<a id="%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B"></a>
## Авторы

© 2018 slowprog  
© 2019-2020 andrey-tech

<a id="%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F"></a>
## Лицензия

Данная библиотека распространяется на условиях лицензии [MIT](./LICENSE).
