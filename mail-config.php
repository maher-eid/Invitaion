<?php
return [
    'mail_to' => getenv('MAIL_TO') ?: 'moudieid2016@gmail.com',
    'mail_from' => getenv('MAIL_FROM') ?: 'noreply@engagement.local',
    'smtp_host' => getenv('SMTP_HOST') ?: '',
    'smtp_username' => getenv('SMTP_USERNAME') ?: '',
    'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
    'smtp_port' => (int) (getenv('SMTP_PORT') ?: 587),
    'smtp_encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls',
];
