<?php

declare(strict_types=1);

namespace App\Infrastructure\Gateway\Notification;

use App\Application\Gateway\Notification\Data;
use App\Application\Gateway\Notification\NotifierInterface;
use App\Domain\ValueObject\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

final readonly class EmailNotifier implements NotifierInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private Email $from,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function notify(Data $data): void
    {
        $to = $data->email;
        if ($to === null) {
            return;
        }

        $email = new MimeEmail()
            ->from($this->from->value)
            ->to($to->value)
            ->subject('Банковская выписка готова')
            ->text($data->message);

        $this->mailer->send($email);
        echo 'Сообщение по почте отправлено' . PHP_EOL;
    }
}
