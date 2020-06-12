<?php
/**
 * Обработчик исключений в YclientsApi
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
 * @version 1.0.0
 *
 * v0.1.0 (27.05.2019) Оригинальная версия от Andrey Tyshev
 * v1.0.0 (27.05.2019) Изменен текст сообщения
 *
 */

declare(strict_types = 1);

namespace Yclients;

class YclientsException extends \Exception
{
    /**
     * Конструктор
     * @param string $message Сообщение об исключении
     * @param int $code Код исключения
     * @param \Exception|null $previous Предыдущее исключение
     */
    public function __construct(string $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct("YclientsApi: " . $message, $code, $previous);
    }
}
