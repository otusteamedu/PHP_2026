<?php

namespace App\Template\Importers;

use App\Template\Entitys\User;

class XmlImporter extends AbstractImporter
{
    protected function load(): void
    {
        var_dump("Получаем данные из xml файла");
        $this->rawData = file_get_contents($this->path);
    }
    protected function parse(): void
    {
        var_dump("Парсим данные из xml файла");
        $this->parsedData = json_decode(json_encode((array)simplexml_load_string($this->rawData)), true);
    }
    protected function validate(): void
    {
        var_dump("Валидируем данные из xml файла");
        if (!isset($this->parsedData['contact'])) throw new \Exception('contact не установлены');
        $this->validatedData = [];
        foreach ($this->parsedData['contact'] as $row) {
            $this->validatedData[] = $row;
        }
    }
    protected function transform(): void
    {
        var_dump("Трансформируем данные из xml файла");
        $this->entitys = [];
        foreach ($this->validatedData as $data) {
            $this->entitys[] = new User($data['first_name'], $data['last_name'], $data['email']);
        }
    }
}