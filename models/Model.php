<?php
class Model {
    protected $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    protected function executeQuery($sql, $params = [], $types = '') {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        return $stmt;
    }

    protected function fetchAll($stmt) {
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}