<?php

declare(strict_types=1);

namespace App\Domain;
enum ValidationMessages: string {
    case INVALID_FORMAT = 'Некорректный адрес электронной почты';
    case INVALID_DOMAIN = 'Ошибка доменного имени у электронной почты';
    case DNS_RECORD_NOT_FOUND = 'Ошибка в DNS записи у адреса электронной почты';
    case UNKNOWN_ERROR = 'Ошибка в адресе электронной почты';
}