<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__.'/../database.php';
include_once __DIR__.'/../model/levelmodel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerLevel();

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'addLevel') {
            echo $controller->addLevel();
            exit();
        } elseif ($_POST['action'] === 'deleteLevel') {
            echo $controller->deleteLevel();
            exit();
        } elseif ($_POST['action'] === 'updateLevel') {
            echo $controller->updateLevel();
            exit();
        }
    } else {
        echo "Error: Action not specified.";
        exit();
    }
}

class ControllerLevel {
    public function addLevel() {
        $name = $_POST['name'] ?? '';
        $subjects = intval($_POST['number_of_seances'] ?? 0);
        $errors = [];

        if (empty($name)) $errors[] = "Le nom du niveau est requis.";
        if ($subjects <= 0) $errors[] = "Le nombre de matières doit être supérieur à zéro.";

        if (!empty($errors)) {
            return "<p style='color:red'>" . implode('<br>', $errors) . "</p>";
        }

        $level = new Level($name, $subjects);
        if ($level->save()) {
            return "<p style='color:green'>Niveau ajouté avec succès !</p>";
        } else {
            return "<p style='color:red'>Erreur lors de l'ajout du niveau.</p>";
        }
    }

    public function deleteLevel() {
        header('Content-Type: application/json');
    
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }
    
        $id = $_POST['id'];
        if (Level::delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Niveau supprimé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
        }
    }
    
    public function updateLevel() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $subjects = intval($_POST['number_of_seances'] ?? 0);

        if (!$id || empty($name) || $subjects <= 0) {
            return json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs valides.']);
        }

        if (Level::update($id, $name, $subjects)) {
            return json_encode(['status' => 'success', 'message' => 'Niveau mis à jour.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function listLevels() {
        return Level::getAll();
    }
}
?>