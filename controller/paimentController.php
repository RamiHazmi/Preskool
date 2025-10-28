<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../database.php';
include_once __DIR__ . '/../model/Paiment.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerPaiment();
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addPaiment':
                echo $controller->addPaiment();
                exit();
            case 'deletePaiment':
                echo $controller->deletePaiment();
                exit();
            case 'updatePaiment':
                echo $controller->updatePaiment();
                exit();
            case 'getPaiementsByStudent':
                $studentId = $_POST['id_student'] ?? '';
                header('Content-Type: application/json');
                echo json_encode($controller->listPaiementsByStudent($studentId));
                exit();
        }
    }else {
        echo "Error: Action not specified.";
        exit();
    }
}

class ControllerPaiment {
    public function addPaiment() {
        $id_student = $_POST['id_student'] ?? '';
        $id_level = $_POST['id_level'] ?? '';
        $montant = $_POST['montant'] ?? '';
        $methode = $_POST['methode'] ?? '';
        $date_paiement = $_POST['date_paiement'] ?? '';
        $date_versement = $_POST['date_versement'] ?? null;

        $errors = [];

        if (empty($id_student)) $errors[] = "ID étudiant est requis.";
        if (empty($id_level)) $errors[] = "ID niveau est requis.";
        if (!is_numeric($montant) || $montant <= 0) $errors[] = "Montant invalide.";
        if (empty($methode)) $errors[] = "Méthode de paiement est requise.";
        if (empty($date_paiement)) $errors[] = "Date de paiement est requise.";
        if (str_starts_with($methode, 'Chèque') && empty($date_versement)) {
            $errors[] = "Date de versement requise pour un chèque.";
        }

        if (!empty($errors)) {
            return "<p style='color:red'>" . implode('<br>', $errors) . "</p>";
        }

        $paiement = new Paiment($id_student, $id_level, $montant, $methode, $date_paiement, $date_versement);
        if ($paiement->save()) {
            return "<p style='color:green'>Paiement ajouté avec succès !</p>";
        } else {
            return "<p style='color:red'>Erreur lors de l'ajout du paiement.</p>";
        }
    }

    public function deletePaiment() {
        header('Content-Type: application/json');

        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }

        $id = $_POST['id'];
        if (Paiment::delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Paiement supprimé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
        }
    }

    public function updatePaiment() {
        $id = $_POST['id'] ?? '';
        $montant = $_POST['montant'] ?? '';
        $methode = $_POST['methode'] ?? '';
        $date_paiement = $_POST['date_paiement'] ?? '';
        $date_versement = $_POST['date_versement'] ?? null;

        if (!$id || !is_numeric($montant) || $montant <= 0 || empty($methode) || empty($date_paiement)) {
            return json_encode(['status' => 'error', 'message' => 'Champs invalides.']);
        }

        if (str_starts_with($methode, 'Chèque') && empty($date_versement)) {
            return json_encode(['status' => 'error', 'message' => 'Date de versement est requise pour un chèque.']);
        }

        if (Paiment::update($id, $montant, $methode, $date_paiement, $date_versement)) {
            return json_encode(['status' => 'success', 'message' => 'Paiement mis à jour avec succès.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function listPaiment() {
        return Paiment::getAll(); // Can be used in view
    }
    public function listPaiementsByStudent($studentId) {
        return Paiment::getPaiementsByStudent($studentId);
    }
    public static function listAllPayments(): array {
        try {
            $conn = config::getConnexion();
            $stmt = $conn->prepare("
                SELECT 
                    MIN(id) AS id,  -- optional: pick min ID per group
                    id_student,
                    DATE_FORMAT(date_paiement, '%Y-%m') AS date_paiement,
                    SUM(montant) AS montant
                FROM paiement
                GROUP BY id_student, DATE_FORMAT(date_paiement, '%Y-%m')
                ORDER BY date_paiement ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Paiment::listAllPayments - " . $e->getMessage());
            return [];
        }
    }
    
    
    
}
