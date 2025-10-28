<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly


include_once __DIR__.'/../database.php';
include_once __DIR__.'/../model/Classroom.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerClassroom();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addClassroom':
                echo $controller->addClassroom();
                exit();
            case 'deleteClassroom':
                echo $controller->deleteClassroom();
                exit();
            case 'updateClassroom':
                echo $controller->updateClassroom();
                exit();
            default:
                echo "Error: Unknown action.";
                exit();
        }
    } else {
        echo "Error: Action not specified.";
        exit();
    }
}

class ControllerClassroom {
    public function addClassroom() {
        $nom = $_POST['nom'] ?? '';
        $errors = [];

        if (empty($nom)) {
            $errors[] = "Le nom de la salle est requis.";
        }

        if (!empty($errors)) {
            return "<p style='color:red'>" . implode('<br>', $errors) . "</p>";
        }

        $classroom = new Classroom($nom);
        if ($classroom->save()) {
            return "<p style='color:green'>Salle ajoutée avec succès !</p>";
        } else {
            return "<p style='color:red'>Erreur lors de l'ajout de la salle.</p>";
        }
    }

    public function deleteClassroom() {
        header('Content-Type: application/json');

        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }

        $id = $_POST['id'];
        if (Classroom::delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Salle supprimée avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
        }
    }

    public function updateClassroom() {
        $id = $_POST['id'] ?? null;
        $nom = $_POST['nom'] ?? '';

        if (!$id || empty($nom)) {
            return json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs valides.']);
        }

        if (Classroom::update($id, $nom)) {
            return json_encode(['status' => 'success', 'message' => 'Salle mise à jour.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function listClassrooms(): array {
        return Classroom::getAll();
    }
}
?>
