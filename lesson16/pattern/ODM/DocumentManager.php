<?php

class DocumentManager
{
    public function __construct(
        private MongoDB\Database $db
    ) {
    }

    public function save(object $document, string $metadataClass): void
    {
        $collection = $metadataClass::collection();
        $fields = $metadataClass::fields();

        $data = [];
        foreach ($fields as $prop => $field) {
            $data[$field] = $document->$prop;
        }

        if ($document->id === null) {
            // INSERT
            unset($data['_id']);
            $data['created_at'] = date('c');

            $result = $this->db->$collection->insertOne($data);
            $document->id = (string)$result->getInsertedId();
        } else {
            // UPDATE
            $id = new MongoDB\BSON\ObjectId($document->id);
            unset($data['_id']);

            $this->db->$collection->updateOne(
                ['_id' => $id],
                ['$set' => $data]
            );
        }
    }

    public function find(string $metadataClass, string $id, string $documentClass): ?object
    {
        $collection = $metadataClass::collection();
        $fields = $metadataClass::fields();

        $row = $this->db->$collection->findOne([
            '_id' => new MongoDB\BSON\ObjectId($id),
        ]);

        if (!$row) {
            return null;
        }

        $row = (array)$row;

        $args = [];
        foreach ($fields as $prop => $field) {
            $args[$prop] = $row[$field] ?? null;
        }

        return new $documentClass(...$args);
    }

    public function all(string $metadataClass, string $documentClass): array
    {
        $collection = $metadataClass::collection();
        $fields = $metadataClass::fields();

        $cursor = $this->db->$collection->find([], ['sort' => ['_id' => -1]]);

        $items = [];
        foreach ($cursor as $row) {
            $row = (array)$row;

            $args = [];
            foreach ($fields as $prop => $field) {
                $args[$prop] = $row[$field] ?? null;
            }

            $items[] = new $documentClass(...$args);
        }

        return $items;
    }
}