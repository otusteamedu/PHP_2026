<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\DTO\ValidationJobPayload;
use App\Application\UseCases\ProcessValidationReportUseCase;
use App\Infrastructure\Queue\Connection;
use App\Infrastructure\Queue\DeathCounter;
use App\Infrastructure\Queue\Topology;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'queue:consume',
    description: 'Consume RabbitMQ queue'
)]
final class ConsumeQueueCommand extends Command
{
    protected static $defaultName = 'queue:consume';

    public function __construct(
        private readonly Connection $connection,
        private readonly Topology $topology,
        private readonly ProcessValidationReportUseCase $useCase
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $channel = $this->connection->channel();
        $this->topology->declare($channel);

        $channel->basic_qos(prefetch_size: 0, prefetch_count: 1, a_global: false);

        $output->writeln('<info>Ожидание сообщений в очереди</info>');

        $callback = function (AMQPMessage $message) use ($output) {
            $this->handleMessage($message, $output);
        };

        $channel->basic_consume(
            queue: Topology::WORK_QUEUE,
            consumer_tag: '',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: $callback
        );

        try {
            while ($channel->is_consuming()) {
                $channel->wait();
            }
        } catch (\Throwable $e) {
            $output->writeln('<error>Фатальная ошибка воркера: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function handleMessage(AMQPMessage $message, OutputInterface $output): void
    {
        $payloadArray = json_decode($message->getBody(), true) ?? [];
        $attempts = DeathCounter::attempts($message);
        
        $output->writeln(sprintf("\n<info>Получено сообщение (Попытка: %d)</info>", $attempts + 1));

        if ($attempts >= Topology::MAX_ATTEMPTS) {
            $this->parkMessage($message, $output);
            return;
        }

        try {
            $payload = ValidationJobPayload::fromArray($payloadArray);
            
            $stats = $this->useCase->execute($payload);

            $output->writeln("[VALIDATION]\nПроверено email: {$stats->total}\nОшибок: {$stats->errors}");
            $pdfSizeKb = round($stats->pdfSize / 1024, 2);
            $output->writeln("[PDF]\nОтчет создан в памяти\nРазмер: {$pdfSizeKb} KB");
            $output->writeln("[MAIL]\nОтправлено пользователю {$payload->reportEmail}");

            $message->ack();
            $output->writeln("<info>Сообщение успешно обработано</info>");

        } catch (\Throwable $e) {
            $output->writeln("<error>Ошибка обработки: {$e->getMessage()}</error>");           
           
            $message->reject(requeue: false);
            $output->writeln("<comment> Сообщение отправлено на повторную попытку через DLX</comment>");
        }
    }

    private function parkMessage(AMQPMessage $message, OutputInterface $output): void
    {
        $channel = $this->connection->channel();

        $parked = new AMQPMessage(
            body: $message->getBody(),
            properties: [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => $message->has('application_headers') 
                    ? $message->get('application_headers') 
                    : null,
            ]
        );

        $channel->basic_publish(
            msg: $parked,
            exchange: Topology::PARKING_LOT_EXCHANGE,
            routing_key: Topology::ROUTING_KEY
        );

        $message->ack();
        $output->writeln("<error>Лимит попыток исчерпан. Сообщение перенесено в parking lot.</error>");
    }
}