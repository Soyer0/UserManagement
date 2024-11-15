<?php
require_once 'Model.php';

class UserModel extends Model {
    public function getAll() {
        $result = $this->db->query("SELECT * FROM users");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($ids) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id IN ($placeholders)");
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($name_first, $name_last, $status, $role_id) {
        $stmt = $this->db->prepare("INSERT INTO users (name_first, name_last, status, role_id) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii", $name_first, $name_last, $status, $role_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        } else {
            return false;
        }
    }

    public function update($id, $name_first, $name_last, $status, $role_id) {
        $stmt = $this->db->prepare("UPDATE users SET name_first = ?, name_last = ?, status = ?, role_id = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $name_first, $name_last, $status, $role_id, $id);
        return $stmt->execute();
    }

    public function delete(array $ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $stmt = $this->db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);

        return $stmt->execute();
    }

    public function setStatus(array $id, $status) {
        $placeholders = implode(',', array_fill(0, count($id), '?'));
        $types = 'i' . str_repeat('i', count($id));

        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id IN ($placeholders)");
        $stmt->bind_param($types, $status,...$id);

        return $stmt->execute();
    }

}