<?php

class Database {
    private $dataPath;

    public function __construct($dataPath = DATA_PATH) {
        $this->dataPath = $dataPath;
        
        // Create data directory if it doesn't exist
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }

    /**
     * Read data from JSON file
     */
    public function read($filename) {
        $filepath = $this->dataPath . $filename . '.json';
        
        if (!file_exists($filepath)) {
            return [];
        }

        $content = file_get_contents($filepath);
        return json_decode($content, true) ?? [];
    }

    /**
     * Write data to JSON file
     */
    public function write($filename, $data) {
        $filepath = $this->dataPath . $filename . '.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return file_put_contents($filepath, $json) !== false;
    }

    /**
     * Insert record into collection
     */
    public function insert($collection, $record) {
        $data = $this->read($collection);
        
        // Generate ID if not present
        if (!isset($record['id'])) {
            $record['id'] = $this->generateId($data);
        }
        
        $data[] = $record;
        $this->write($collection, $data);
        
        return $record;
    }

    /**
     * Find records by criteria
     */
    public function find($collection, $criteria = []) {
        $data = $this->read($collection);
        
        if (empty($criteria)) {
            return $data;
        }

        return array_filter($data, function($record) use ($criteria) {
            foreach ($criteria as $key => $value) {
                if (!isset($record[$key]) || $record[$key] !== $value) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Find one record by criteria
     */
    public function findOne($collection, $criteria) {
        $results = $this->find($collection, $criteria);
        return !empty($results) ? reset($results) : null;
    }

    /**
     * Update records by criteria
     */
    public function update($collection, $criteria, $updates) {
        $data = $this->read($collection);
        $updated = false;

        foreach ($data as &$record) {
            $match = true;
            foreach ($criteria as $key => $value) {
                if (!isset($record[$key]) || $record[$key] !== $value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                foreach ($updates as $key => $value) {
                    $record[$key] = $value;
                }
                $updated = true;
            }
        }

        if ($updated) {
            $this->write($collection, $data);
        }

        return $updated;
    }

    /**
     * Delete records by criteria
     */
    public function delete($collection, $criteria) {
        $data = $this->read($collection);
        $originalCount = count($data);

        $data = array_filter($data, function($record) use ($criteria) {
            foreach ($criteria as $key => $value) {
                if (!isset($record[$key]) || $record[$key] !== $value) {
                    return true;
                }
            }
            return false;
        });

        $deletedCount = $originalCount - count($data);

        if ($deletedCount > 0) {
            $this->write($collection, array_values($data));
        }

        return $deletedCount;
    }

    /**
     * Generate unique ID
     */
    private function generateId($data) {
        if (empty($data)) {
            return 1;
        }

        $ids = array_column($data, 'id');
        return max($ids) + 1;
    }

    /**
     * Check if collection exists
     */
    public function exists($collection) {
        return file_exists($this->dataPath . $collection . '.json');
    }

    /**
     * Delete collection
     */
    public function dropCollection($collection) {
        $filepath = $this->dataPath . $collection . '.json';
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}

