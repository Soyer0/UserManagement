<?php
require_once __DIR__ .'/../models/UserModel.php';

class UserController {
    private $userModel;
    private $roles = [1 => 'Admin', 2 => 'User'];
    private $status = [0 => 'not-active', 1 => 'active'];
    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function getUserInputData() {
        $userId = $_POST['userId'] ?? null;
        $name_first = trim($_POST['firstName'] ?? '');
        $name_last = trim($_POST['lastName'] ?? '');
        $status = $_POST['status'] ?? null;
        $role_id = $_POST['role_id'] ?? null;

        return [
            'id' => $userId,
            'name_first' => $name_first,
            'name_last' => $name_last,
            'status' => $status,
            'role_id' => $role_id,
        ];
    }

    private function prepareUserResponse($data)
    {
        $isMultiple = isset($data[0]) && is_array($data[0]);

        if ($isMultiple) {
            $users = array_map(function ($user) {
                $user['role_name'] = $this->roles[$user['role_id']] ?? 'Unknown';
                $user['status'] = $this->status[$user['status']] ?? 0;
                return $user;
            }, $data);
        } else {
            $data['role_name'] = $this->roles[$data['role_id']] ?? 'Unknown';
            $data['status'] = $this->status[$data['status']] ?? 0;
            $users = $data;
        }
        return [
            'success' => true,
            ($isMultiple ? 'users' : 'user') => $users,
            'error' => null,
        ];

    }




    public function showUsers() {
        $users = $this->userModel->getAll();

        $content = $this->render('users/index', [
            'users' => $users,
            'roles' => $this->roles,
            'status' => $this->status,
        ]);

        echo $this->render('layout', ['content' => $content]);
    }

    public function addUser() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $this->getUserInputData();

                if ($data['name_first'] === '' || $data['name_last'] === '' || $data['status'] === null || $data['role_id'] === null) {
                    throw new Exception("Invalid input data");
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
            if ($data['name_first'] === '' || $data['name_last'] === '' || $data['status'] === null || $data['role_id'] === null) {
                throw new Exception("Invalid input data");
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