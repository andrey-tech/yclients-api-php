<?php
/**
 * Трейт YclientsRequest. Отправляет запросы к API YClients.
 *
 * Original author: Andrey Tyshev
 * @author Andrey Tyshev (slowprog)
 * @see https://github.com/slowprog/yclients-api
 * @copyright 2018 Andrey Tyshev
 * @license MIT
 *
 * @author    andrey-tech
 * @copyright 2019-2020 andrey-tech
 * @see https://github.com/andrey-tech/amocrm-api-php
 * @license MIT
 *
 */

declare(strict_types = 1);

namespace Yclients;

trait YclientsRequest
{
    /**
     * Флаг включения вывода отладочной информации
     * @var bool
     */
    public $debug = false;

    /**
     * Лог файл для сохранения отладочной информации (относительно каталога файла данного класса)
     * (null - вывод в STDOUT)
     * @var string|null
     */
    public $debugLogFile = 'logs/debug.log';

    /**
     * Максимальное число запросов к API в секунду (не более 5!)
     * @var float
     */
    public $throttle = 5;

    /**
     * Флаг включения проверки SSL-сертификата сервера YClients
     * @var bool
     */
    public $verifySSLCerfificate = true;

    /**
     * Файл SSL-сертификатов X.509 корневых удостоверяющих центров (относительно каталога файла данного класса)
     * (null - файл, указанный в настройках php.ini)
     * @var string
     */
    public $sslCertificateFile = 'cacert.pem';

    /**
     * Таймаут соединения с сервером Yclients для сUrl, секунд
     * @var integer
     */
    public $curlTimeout = 30;

    /**
     * Максимальное количество сушностей, загружаемых на один запрос в методе getAll()
     * @var int
     */
    public $limitCount = 300;

    /**
     * Время последнего запроса к API
     * @var float
     */
    protected $lastRequestTime = 0;

    /**
     * Счетчик числа запросов для отладочных сообщений
     * @var integer
     */
    protected $requestCounter = 0;

    /**
     * Уникальное значение ID для метки в отладочных сообщениях
     * @var string
     */
    protected $uniqId;

    /**
     * Подготовка запроса
     *
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @param bool|string $auth - если true, то авторизация партнёрская
     *                            если string, то авторизация пользовательская
     * @return array
     * @access protected
     * @throws YclientsException
     */
    public function request($url, $parameters = [], $method = 'GET', $auth = true)
    {
        $headers = [ 'Content-Type: application/json' ];

        if ($auth) {
            if (!$this->tokenPartner) {
                throw new YclientsException('Не указан токен партнёра');
            }

            $headers[] = 'Authorization: Bearer ' . $this->tokenPartner . (is_string($auth) ? ', User ' . $auth : '');
        }

        return $this->requestCurl($url, $parameters, $method, $headers);
    }

    /**
     * Выполнение непосредственно запроса с помощью curl
     *
     * @param string $url
     * @param array $parameters
     * @param string $method
     * @param array $headers
     * @return array
     * @access protected
     * @throws YclientsException
     */
    protected function requestCurl($url, $parameters = [], $method = 'GET', $headers = [])
    {
        // Увеличиваем счетчик числа отправленных запросов
        $this->requestCounter++;

        $ch = curl_init();

        $jsonParameters = '{}';
        if (count($parameters)) {
            if ($method === self::METHOD_GET) {
                $url .= '?' . http_build_query($parameters);
            } else {
                $jsonParameters = json_encode($parameters);
                if ($jsonParameters === false) {
                    $code = json_last_error();
                    throw new YclientsException("Ошибка кодирования JSON ({$code}): " . print_r($parameters, true));
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonParameters);
            }
        }

        if ($method === self::METHOD_GET) {
            $this->debug("[{$this->requestCounter}] ===> GET {$url}");
        } elseif ($method === self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            $this->debug("[{$this->requestCounter}] ===> POST {$url}" . PHP_EOL . $jsonParameters);
        } elseif ($method === self::METHOD_PUT) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_PUT);
            $this->debug("[{$this->requestCounter}] ===> PUT {$url}" . PHP_EOL . $jsonParameters);
        } elseif ($method === self::METHOD_DELETE) {
            $this->debug("[{$this->requestCounter}] ===> DELETE: {$url}" . PHP_EOL . $jsonParameters);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
        }

        curl_setopt($ch, CURLOPT_URL, self::URL . '/' . $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Включение проверки SSL-сертификата сервера YClients
        if ($this->verifySSLCerfificate) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            if ($this->sslCertificateFile) {
                $sslCertificateFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->sslCertificateFile;
                curl_setopt($ch, CURLOPT_CAINFO, $sslCertificateFile);
            }
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if (count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = $this->throttleCurl($ch);
        $deltaTime = sprintf('%0.4f', microtime(true) - $this->lastRequestTime);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        // Проверка ошибок cURL
        if ($errno) {
            throw new YclientsException("Oшибка cURL ({$errno}): {$error}");
        }

        // Если в ответе были переданы только заголовки без тела сообщения (204 No Content)
        if ($code === 204) {
            $this->debug(
                "[{$this->requestCounter}] <=== RESPONSE {$deltaTime}s ({$code})" . PHP_EOL . print_r($result, true)
            );
            return null;
        }

        // Декодирование JSON ответа сервера
        $response = json_decode($result, true);
        $this->debug(
            "[{$this->requestCounter}] <=== RESPONSE {$deltaTime}s ({$code})" . PHP_EOL . print_r($response, true)
        );
        if (is_null($response)) {
            $code = json_last_error();
            throw new YclientsException("Ошибка декодирования JSON ({$code}): {$result}");
        }

        // Если сообщение об ошибке в ответе сервера
        if (isset($response['errors'])) {
            throw new YclientsException(
                'Ошибка: ' . print_r($response['errors'], true) . '. Request: ' . $jsonParameters
            );
        }

        // Если сообщение об ошибке success:false в ответе сервера
        if (isset($response['success']) && ! $response['success']) {
            throw new YclientsException('Ошибка: ' . print_r($response, true) . '. Request: ' . $jsonParameters);
        }

        return $response;
    }

    /**
     * Загружает все сущности заданного типа
     * @param  object $callback Анонимная функция для загрузки сущностей: $callback($page, $count)
     *                          $page - номер страницы, $count - число сущностей на странице
     * @return \Generator
     */
    public function getAll($callback) :\Generator
    {
        $page = 1;

        while (true) {
            $response = $callback($page, $this->limitCount);
            $data = $response['data'];
          
            yield $response;

            if (count($data) < $this->limitCount) {
                break;
            }

            $page++;
        }
    }

    /**
     * Обеспечивает троттлинг запросов к YClients API
     * @param resource $curl
     * @return string|null
     */
    protected function throttleCurl($curl)
    {
        do {
            $usleep = (int) (1E6 * ($this->lastRequestTime + 1/$this->throttle - microtime(true)));
            if ($usleep <= 0) {
                break;
            }

            $throttleTime = sprintf('%0.4f', $usleep/1E6);
            $this->debug("[{$this->requestCounter}] ++++ THROTTLE ({$this->throttle}) {$throttleTime}s");

            usleep($usleep);
        } while (false);

        $this->lastRequestTime = microtime(true);

        $result = curl_exec($curl);

        return $result;
    }

    /**
     * Выводит отладочные сообщения
     * @param string $message
     * @return void
     */
    // ------------------------------------------------------------------------
    protected function debug(string $message = '')
    {
        if (! $this->debug) {
            return;
        }

        // Формируем строку времени логгирования
        $dateTime = \DateTime::createFromFormat('U.u', sprintf('%.f', microtime(true)));
        $timeZone = new \DateTimeZone(date_default_timezone_get());
        $dateTime->setTimeZone($timeZone);
        $timeString = $dateTime->format('Y-m-d H:i:s,u P');

        $uniqId = $this->getUniqId();
        $message = "*** {$uniqId} [{$timeString}]" . PHP_EOL . $message . PHP_EOL . PHP_EOL;

        // Если лог файл не указан, то вывод в STDOUT
        if (empty($this->debugLogFile)) {
            echo $message . PHP_EOL;
            return;
        }

        // Формируем полное имя лог файла
        $debugLogFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->debugLogFile;
        $this->checkDir(dirname($debugLogFile));

        // Записываем сообщение в лог файл
        if (! file_put_contents($debugLogFile, $message, FILE_APPEND|LOCK_EX)) {
            throw new YclientsException("Не удалось записать в лог файл {$debugLogFile}");
        }
    }

    /**
     * Возвращает уникальное значение ID для метки в отладочных сообщениях
     * @param  int $length Длина ID
     * @return string
     */
    protected function getUniqId(int $length = 7) :string
    {
        if (! isset($this->uniqId)) {
            $this->uniqId = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $length);
        }
        return $this->uniqId;
    }

    /**
     * Проверяет наличие каталога для сохранения файла и создает каталог при его отсутствии рекурсивно
     * @param string $directory Полный путь к каталогу
     * @return void
     */
    protected function checkDir(string $directory)
    {
        // Выходим, если каталог уже есть (is_dir кешируется PHP)
        if (is_dir($directory)) {
            return;
        }

        // Создаем новый каталог рекурсивно
        if (! mkdir($directory, $mode = 0755, $recursive = true)) {
            throw new YclientsException("Не удалось рекурсивно создать каталог {$directory}");
        }
    }
}
