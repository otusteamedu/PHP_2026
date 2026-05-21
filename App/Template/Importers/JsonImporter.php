<?php

namespace App\Template\Importers;

use App\Template\Entitys\User;

class JsonImporter extends AbstractImporter
{
    protected function load(): void
    {
        var_dump("Получаем данные из json файла");
        $this->rawData = file_get_contents($this->path);
    }
    protected function parse(): void
    {
        var_dump("Парсим данные из json файла");
        $this->parsedData = json_decode($this->rawData, true);
    }
    protected function validate(): void
    {
        var_dump("Валидируем данные из json файла");
        if (!isset($this->parsedData['items'])) throw new \Exception('items не установлены');
        $this->validatedData = [];
        foreach ($this->parsedData['items'] as $row) {
            $this->validatedData[] = $row;
        }
    }
    protected function transform(): void
    {
        var_dump("Трансформируем данные из json файла");
        $this->entitys = [];
        foreach ($this->validatedData as $data) {
            $this->entitys[] = new User($data['first_name'], $data['last_name'], $data['email']);
        }
    }
}