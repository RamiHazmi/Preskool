<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../database.php';
include_once __DIR__ . '/../model/Enseignant.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerEnseignant();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addEnseignant':
                echo $controller->addEnseignant();
                break;
            case 'deleteEnseignant':
                echo $controller->deleteEnseignant();
                break;
            case 'updateEnseignant':
                echo $controller->updateEnseignant();
                break;
        }
        exit();
    }
}

class ControllerEnseignant {
    public function addEnseignant() {
        header('Content-Type: application/json'); // ensure it's JSON
    
        $nom = $_POST['nom'] ?? '';
        $id_matiere = $_POST['id_matiere'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
    
        $errors = [];
        if (empty($nom)) $errors[] = "Nom requis.";
        if (empty($id_matiere)) $errors[] = "Matière requise.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
        if (empty($phone)) $errors[] = "Téléphone requis.";
    
        if (!empty($errors)) {
            return json_encode([
                'success' => false,
                'message' => implode(' ', $errors)
            ]);
        }
    
        $enseignant = new Enseignant($nom, $id_matiere, $email, $phone);
        if ($enseignant->save()) {
            return json_encode([
                'success' => true,
                'message' => "Enseignant ajouté avec succès !"
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => "Erreur lors de l'ajout."
            ]);
        }
    }
    
    public function deleteEnseignant() {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }

        if (Enseignant::delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Supprimé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression.']);
        }
    }

    public function updateEnseignant() {
        $id = $_POST['id'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $id_matiere = $_POST['id_matiere'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (empty($id) || empty($nom) || empty($id_matiere) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($phone)) {
            return json_encode(['status' => 'error', 'message' => 'Champs invalides.']);
        }

        if (Enseignant::update($id, $nom, $id_matiere, $email, $phone)) {
            return json_encode(['status' => 'success', 'message' => 'Mis à jour avec succès.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function listEnseignants(): array {
        return Enseignant::getAll();
    }
}
?>
