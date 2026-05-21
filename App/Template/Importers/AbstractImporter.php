<?php

namespace App\Template\Importers;

abstract class AbstractImporter
{
    protected string $rawData;
    protected array $parsedData;
    protected array $validatedData;
    protected array $entitys;

    public function __construct(protected string $path) {}
    protected mixed $data;
    /**
     * Финальный метод — алгоритм импорта, нельзя переопределить
     */
    final public function import(): void
    {
        $this->load();
        $this->parse();
        $this->validate();
        $this->transform();


        // Общая логика — сохранение в БД
        $this->saveToDatabase();
    }

    /**
     * Загрузить данные из источника
     */
    abstract protected function load(): void;

    /**
     * Распарсить данные
     */
    abstract protected function parse(): void;

    /**
     * Валидировать данные
     */
    abstract protected function validate(): void;

    /**
     * Преобразовать в доменные объекты
     */
    abstract protected function transform(): void;

    /**
     * Hook-метод, который можно переопределить в наследниках
     */
    protected function beforeSave(): void
    {
        // Пустая реализация по умолчанию
    }

    /**
     * Общая логика — сохранение в БД (должна быть в базовом классе)
     */
    private function saveToDatabase(): void
    {
        echo "Сохранение " . count($this->entitys) . " объектов в БД...\n";
        // Здесь может быть реальная логика сохранения в БД
    }
}