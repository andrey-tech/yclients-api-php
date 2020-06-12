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
