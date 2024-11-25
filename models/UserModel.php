<?php
require_once 'Model.php';

class UserModel extends Model {
    public function getAll() {
        $stmt = $this->executeQuery("SELECT * FROM users");
        return $this->fetchAll($stmt);
    }

    public function getById($ids) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $this->executeQuery("SELECT * FROM users WHERE id IN ($placeholders)", $ids, $types);
        return $this->fetchAll($stmt);
    }

    public function add($name_first, $name_last, $status, $role_id) {
        $stmt = $this->executeQuery(
            "INSERT INTO users (name_first, name_last, status, role_id) VALUES (?, ?, ?, ?)",
            [$name_first, $name_last, $status, $role_id],
            "ssii"
        );
        return $this->db->insert_id;
    }

    public function update($id, $name_first, $name_last, $status, $role_id) {
        $stmt = $this->executeQuery(
            "UPDATE users SET name_first = ?, name_last = ?, status = ?, role_id = ? WHERE id = ?",
            [$name_first, $name_last, $status, $role_id, $id],
            "ssiii"
        );
        return $stmt->affected_rows > 0;
    }

    public function delete(array $ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $this->executeQuery("DELETE FROM users WHERE id IN ($placeholders)", $ids, $types);
        return $stmt->affected_rows > 0;
    }

    public function setStatus(array $ids, $status) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = 'i' . str_repeat('i', count($ids));
        $params = array_merge([$status], $ids);
        $stmt = $this->executeQuery("UPDATE users SET status = ? WHERE id IN ($placeholders)", $params, $types);
        return $stmt->affected_rows > 0;
    }

}