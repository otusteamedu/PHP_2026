<?php

namespace App\Template\Importers;

use App\Template\Entitys\User;

class CsvImporter extends AbstractImporter
{
    protected function load(): void
    {
        var_dump("Получаем данные из csv файла");
        $this->rawData = file_get_contents($this->path);
    }

    protected function parse(): void
    {
        var_dump("Парсим данные из csv файла");
        $rows = explode("\n", $this->rawData);
        $this->parsedData = [];

        for ($i = 0; $i < count($rows); $i++) {
            $this->parsedData[] = explode(';', $rows[$i]);
        }
    }

    protected function validate(): void
    {
        var_dump("Валидируем данные из csv файла");
        $headers = array_shift($this->parsedData);
        $countColumns = count($headers);
        if ($countColumns !== 3) throw new \Exception('В файле должно быть 3 колонки');

        $column1 = array_shift($headers);
        if ($column1 !== 'first_name') throw new \Exception('Первая колонка должна быть "first_name"');

        $column2 = array_shift($headers);
        if ($column2 !== 'last_name') throw new \Exception('Вторая колонка должна быть "last_name"');

        $column3 = substr(array_shift($headers), 0, -1);

        if ($column3 !== "email") throw new \Exception('Третья колонка должна быть "email"');

        $this->validatedData = [];
        foreach ($this->parsedData as $row) {
            if ($row[0] && $row[1] && $row[2]) {
                $this->validatedData[] = [
                    'first_name' => $row[0],
                    'last_name' => $row[1],
                    'email' => $row[2],
                ];
            }
        }
    }

    protected function transform(): void
    {
        var_dump("Трансформируем данные из csv файла");
        $this->entitys = [];
        foreach ($this->validatedData as $data) {
            $this->entitys[] = new User($data['first_name'], $data['last_name'], $data['email']);
        }
    }
}