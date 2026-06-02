<?php

declare(strict_types=1);

abstract class AbstractImporter
{
    protected string $loadedData;

    public function __construct(protected string $data)
    {}
    
    final public function import(): array
    {
        try {
            $loadedData = $this->load($this->data);
            $parsedData = $this->parse($loadedData);
            $validData  = $this->validate($parsedData);   
            $entities   = $this->transform($validData); 
            $this->beforeSave($entities);
            return $this->saveToDb($entities);            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data'    => 'Ошибка импорта: ' . $e->getMessage()
            ];
        }
    }

    abstract protected function load(string $loaded);

    abstract protected function parse(string $loadedData);

    abstract protected function validate(array $parsedData);

    abstract protected function transform(array $validData);

    protected function saveToDb(array $entities): array
    {
        return [
            'success' => true,
            'data'    => "Успешно сохранено в БД " . count($entities) . " объектов",
        ];
    }

    protected function beforeSave(array $entities): void
    {

    }
}