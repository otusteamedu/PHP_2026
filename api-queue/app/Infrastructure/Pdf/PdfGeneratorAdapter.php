<?php

declare(strict_types=1);

namespace App\Infrastructure\Pdf;

use App\Application\Ports\PdfGeneratorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

final class PdfGeneratorAdapter implements PdfGeneratorInterface
{
    /**
     * @param array<int, array{email:string,status:string,message:string}> $validationResults
     */
    public function generate(array $validationResults): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // Важно для поддержки кириллицы
        
        $dompdf = new Dompdf($options);
        
        $date = new \DateTimeImmutable();
        $date = $date->format('d.m.Y');
        $rows = '';
        
        foreach ($validationResults as $result) {
            $rows .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
                htmlspecialchars($result['email']),
                htmlspecialchars($result['status']),
                htmlspecialchars($result['message'])
            );
        }

        $html = <<<HTML
        <html>
        <head>
            <style>
                body { font-family: 'DejaVu Sans', sans-serif; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h2>Отчет проверки электронных адресов</h2>
            <p>Дата создания отчета: {$date}</p>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Статус</th>
                        <th>Сообщение</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>
        </body>
        </html>
        HTML;

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}