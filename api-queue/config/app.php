<?php

return [
    'max_emails_in_request' => 10,
    'mail_driver'=> getenv('MAIL_DRIVER') ?: 'fake',   
    'smtp_dsn' => getenv('SMTP_DSN') ?: '',  
    'mail_from' => getenv('MAIL_FROM_ADDRESS') ?: 'otus@otus.ru',
];