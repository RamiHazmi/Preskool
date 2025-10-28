<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../database.php';
include_once __DIR__ . '/../model/Class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerClass();

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'addClass') {
            echo $controller->addClass();
            exit();
        } elseif ($_POST['action'] === 'deleteClass') {
            echo $controller->deleteClass();
            exit();
        } elseif ($_POST['action'] === 'updateClass') {
            echo $controller->updateClass();
            exit();
        }
    } else {
        echo "Error: Action not specified.";
        exit();
    }
}

class ControllerClass {
    public function addClass() {
        $level_id = $_POST['level_id'] ?? '';
        $class_name = $_POST['class_name'] ?? '';

        $errors = [];
        if (empty($level_id)) $errors[] = "Le niveau est requis.";
        if (empty($class_name)) $errors[] = "Le nom de la classe est requis.";

        if (!empty($errors)) {
            return "<p style='color:red'>" . implode('<br>', $errors) . "</p>";
        }

        $class = new ClassModel($class_name, $level_id);

        if ($class->save()) {
            return "<p style='color:green'>Classe ajoutée avec succès !</p>";
        } else {
            return "<p style='color:red'>Erreur lors de l'ajout de la classe.</p>";
        }
    }

    public function deleteClass() {
        header('Content-Type: application/json');

        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }

        $id = $_POST['id'];

        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM class WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(['status' => 'success', 'message' => 'Classe supprimée avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    public function updateClass() {
        $id = $_POST['id'] ?? '';
        $level_id = $_POST['level_id'] ?? '';
        $class_name = $_POST['class_name'] ?? '';

        if (empty($id) || empty($level_id) || empty($class_name)) {
            return json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']);
        }

        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE class SET class_name = ?, level_id = ? WHERE id = ?");
            if ($stmt->execute([$class_name, $level_id, $id])) {
                return json_encode(['status' => 'success', 'message' => 'Classe mise à jour avec succès.']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
            }
        } catch (PDOException $e) {
            return json_encode(['status' => 'error', 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    public function listClasses() {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("
                SELECT 
                    c.*, 
                    l.name AS level_name,
                    (
                        SELECT COUNT(*) 
                        FROM student s 
                        WHERE s.class_id = c.id
                    ) AS number_of_students
                FROM class c
                LEFT JOIN level l ON c.level_id = l.id
                ORDER BY c.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching classes: " . $e->getMessage());
            return [];
        }
    
}
}
?>
