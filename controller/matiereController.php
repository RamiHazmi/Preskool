<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../database.php';
include_once __DIR__ . '/../model/matiereModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerMatiere();

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'addMatiere') {
            echo $controller->addMatiere();
            exit();
        } elseif ($_POST['action'] === 'deleteMatiere') {
            echo $controller->deleteMatiere();
            exit();
        } elseif ($_POST['action'] === 'updateMatiere') {
            echo $controller->updateMatiere();
            exit();
        }
    } else {
        echo "Error: Action not specified.";
        exit();
    }
}

class ControllerMatiere {

    public function addMatiere() {
        $nom = $_POST['nom'] ?? '';
        if (empty($nom)) {
            return json_encode([
                'status' => 'error',
                'message' => "Le nom de la matière est requis."
            ]);
        }
    
        $matiere = new Matiere($nom);
        if ($matiere->save()) {
            return json_encode([
                'status' => 'success',
                'message' => "Matière ajoutée avec succès !"
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => "Erreur lors de l'ajout de la matière."
            ]);
        }
    }

    public function deleteMatiere() {
        header('Content-Type: application/json');
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }

        $id = $_POST['id'];
        if (Matiere::delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Matière supprimée avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
        }
    }

    public function updateMatiere() {
        header('Content-Type: application/json'); // Add this line
        $id = $_POST['id'] ?? null;
        $nom = $_POST['nom'] ?? '';

        if (!$id || empty($nom)) {
            return json_encode(['status' => 'error', 'message' => 'Champs invalides.']);
        }

        if (Matiere::update($id, $nom)) {
            return json_encode(['status' => 'success', 'message' => 'Matière mise à jour.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function listMatieres() {
        return Matiere::getAll();
    }
}
