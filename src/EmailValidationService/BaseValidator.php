<?php

declare(strict_types=1);

namespace App\EmailValidationService;

class BaseValidator implements EmailValidationInterface
{

    public static function checkEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $result = self::checkDnsMx($email);

        return $result['success'] ?? false;
    }

    private static function checkDnsMx(string $email): array
    {
        $domain = explode('@', $email)[1];

        $domain = parse_url($domain, PHP_URL_HOST) ?: $domain;

        $records = dns_get_record($domain, DNS_MX);

        if (empty($records)) {
            return [
                'success' => false,
                'error' => 'No MX records found or domain does not exist'
            ];
        }

        // Sort by priority
        usort($records, function($a, $b) {
            return $a['pri'] - $b['pri'];
        });

        return [
            'success' => true,
            'domain' => $domain,
            'has_mx' => true,
            'records' => array_map(function($record) {
                return [
                    'priority' => $record['pri'],
                    'server' => rtrim($record['target'], '.')
                ];
            }, $records)
        ];
    }
}
