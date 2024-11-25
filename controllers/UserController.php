<?php
require_once __DIR__ .'/../models/UserModel.php';

class UserController {
    private $userModel;
    private $roles = [1 => 'Admin', 2 => 'User'];
    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function getUserInputData() {
        return [
            'id' => (int)($_POST['userId'] ?? 0),
            'name_first' => trim($_POST['firstName'] ?? ''),
            'name_last' => trim($_POST['lastName'] ?? ''),
            'status' => $_POST['status'] ?? 0,
            'role_id' => (int)($_POST['role_id'] ?? 0),
        ];
    }

    private function validateUserData($data) {
        $errors = [];

        if (empty($data['name_first'])) {
            $errors['firstName'] = 'First name is required.';
        }

        if (empty($data['name_last'])) {
            $errors['lastName'] = 'Last name is required.';
        }

        if (!in_array($data['role_id'], array_keys($this->roles))) {
            $errors['role_id'] = 'Invalid role selected.';
        }

        return $errors;
    }

    private function prepareUserResponse($data)
    {
        $key = isset($data[0]) && is_array($data[0]) ? 'users' : 'user';
        return [
            'success' => true,
            $key => $data,
            'error' => null,
        ];
    }


    public function showUsers() {
        $users = $this->userModel->getAll();

        $content = $this->render('users/index', [
            'users' => $users,
            'roles' => $this->roles,
        ]);

        echo $this->render('layout', ['content' => $content]);
    }

    public function addUser() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $this->getUserInputData();

                $errors = $this->validateUserData($data);

                if (!empty($errors)) {
                    throw new Exception(json_encode($errors));
                }

                $id = $this->userModel->add($data['name_first'], $data['name_last'], $data['status'], $data['role_id']);
                if (!$id) {
                    throw new Exception("Failed to add user");
                }

                $users = $this->userModel->getById($id);
                if (empty($users)) {
                    throw new Exception("Failed to retrieve user");
                }
                $user = $users[0];

                $response = $this->prepareUserResponse($user);
                echo json_encode($response);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function editUser() {
        header('Content-Type: application/json');
        $data = $this->getUserInputData();

        if ($data['id'] <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
            exit;
        }

        try {
            $errors = $this->validateUserData($data);

            if (!empty($errors)) {
                throw new Exception(json_encode($errors));
            }

            $updated = $this->userModel->update($data['id'], $data['name_first'], $data['name_last'], $data['status'], $data['role_id']);
            if (!$updated) {
                throw new Exception("Failed to update user");
            }

            $users = $this->userModel->getById($data['id']);
            if (empty($users)) {
                throw new Exception("Failed to retrieve user");
            }
            $user = $users[0];

            $response = $this->prepareUserResponse($user);
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function deleteUsers() {
    header('Content-Type: application/json');
        $ids = $_POST['userIds'] ?? [];

        if (count($ids) === 0) {
            echo json_encode(['status' => false, 'error' => 'No users selected for deletion']);
            exit;
        }

        try {
            $deleted = $this->userModel->delete($ids);
            if (!$deleted) {
                throw new Exception("Failed to delete user");
            }

            echo json_encode([
                'success' => true,
                'error' => null,
                'users' => $ids
                ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function setStatus() {
        header('Content-Type: application/json');
        $ids = $_POST['userIds'] ?? [];
        $status = $_POST['status'] ?? null;

        if ($ids <= 0 || $status === null) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID or status']);
            exit;
        }

        try {
            $this->userModel->setStatus($ids, $status);
            $updatedUsers = $this->userModel->getById($ids);

            if (!$updatedUsers) {
                throw new Exception("Failed to update user status");
            }

            if(count($updatedUsers) !== count($ids)) {
                throw new Exception("Failed to retrieve updated users");
            }

            $response = $this->prepareUserResponse($updatedUsers);
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode([
               'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getUser() {
        header('Content-Type: application/json');
        $id = $_GET['id']?? 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
            exit;
        }

        try {
            $users = $this->userModel->getById($id);
            if (empty($users)) {
                throw new Exception("Failed to retrieve user");
            }
            $user = $users[0];

            $response = $this->prepareUserResponse($user);
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    private function render($view, $data = []) {
        extract($data);
        ob_start();
        include "views/$view.php";
        return ob_get_clean();
    }
}