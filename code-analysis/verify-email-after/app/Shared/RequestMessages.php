<?php

declare(strict_types=1);

namespace App\Shared;

enum RequestMessages: string {
    case JSON_ERROR = 'Ошибка при парсинге объекта JSON в запросе';
    case EMPTY_REQUEST_BODY = 'Ошибка в теле запроса';
    case MISSING_EMAIL_ADDRESS = 'В запросе отсутствует электронный адрес';
    case ERROR_EADDRES_COUNT = 'Ошибка в количестве электронных адресов';
}