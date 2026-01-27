<?php

class Database {
    private $client;
    private $database;

    public function __construct() {
        $this->client = new MongoDB\Client(MONGODB_URI);
        $this->database = $this->client->selectDatabase(MONGODB_DATABASE);
    }

    /**
     * Get collection
     */
    private function getCollection($name) {
        return $this->database->selectCollection($name);
    }

    /**
     * Read all data from collection
     */
    public function read($collection) {
        $cursor = $this->getCollection($collection)->find();
        $results = [];
        foreach ($cursor as $document) {
            $results[] = $this->documentToArray($document);
        }
        return $results;
    }

    /**
     * Write data to collection (replaces all documents)
     */
    public function write($collection, $data) {
        $col = $this->getCollection($collection);
        $col->deleteMany([]);
        
        if (!empty($data)) {
            $documents = array_map(function($item) {
                return $this->prepareDocument($item);
            }, $data);
            $col->insertMany($documents);
        }
        
        return true;
    }

    /**
     * Insert record into collection
     */
    public function insert($collection, $record) {
        $col = $this->getCollection($collection);
        
        // Generate ID if not present
        if (!isset($record['id'])) {
            $record['id'] = $this->generateId($collection);
        }
        
        $col->insertOne($this->prepareDocument($record));
        
        return $record;
    }

    /**
     * Find records by criteria
     */
    public function find($collection, $criteria = []) {
        $col = $this->getCollection($collection);
        $cursor = $col->find($criteria);
        
        $results = [];
        foreach ($cursor as $document) {
            $results[] = $this->documentToArray($document);
        }
        
        return $results;
    }

    /**
     * Find one record by criteria
     */
    public function findOne($collection, $criteria) {
        $col = $this->getCollection($collection);
        $document = $col->findOne($criteria);
        
        return $document ? $this->documentToArray($document) : null;
    }

    /**
     * Update records by criteria
     */
    public function update($collection, $criteria, $updates) {
        $col = $this->getCollection($collection);
        $result = $col->updateMany($criteria, ['$set' => $updates]);
        
        return $result->getModifiedCount() > 0;
    }

    /**
     * Delete records by criteria
     */
    public function delete($collection, $criteria) {
        $col = $this->getCollection($collection);
        $result = $col->deleteMany($criteria);
        
        return $result->getDeletedCount();
    }

    /**
     * Generate unique ID
     */
    private function generateId($collection) {
        $col = $this->getCollection($collection);
        $lastDoc = $col->findOne([], [
            'sort' => ['id' => -1],
            'projection' => ['id' => 1]
        ]);
        
        return $lastDoc ? ($lastDoc['id'] + 1) : 1;
    }

    /**
     * Check if collection exists and has documents
     */
    public function exists($collection) {
        $col = $this->getCollection($collection);
        return $col->countDocuments() > 0;
    }

    /**
     * Delete collection
     */
    public function dropCollection($collection) {
        $this->getCollection($collection)->drop();
        return true;
    }

    /**
     * Convert MongoDB document to array
     */
    private function documentToArray($document) {
        $array = (array) $document;
        unset($array['_id']); // Remove MongoDB's internal _id
        
        // Recursively convert nested objects
        foreach ($array as $key => $value) {
            if ($value instanceof MongoDB\Model\BSONArray) {
                $array[$key] = $value->getArrayCopy();
            } elseif ($value instanceof MongoDB\Model\BSONDocument) {
                $array[$key] = (array) $value;
            }
        }
        
        return $array;
    }

    /**
     * Prepare document for insertion
     */
    private function prepareDocument($data) {
        // Ensure arrays are properly formatted for MongoDB
        return $data;
    }
}
