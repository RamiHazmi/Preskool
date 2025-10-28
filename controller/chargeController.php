<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../database.php';
include_once __DIR__ . '/../model/chargemodel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerCharge();

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'addCharge') {
            echo $controller->addCharge();
            exit();
        } elseif ($_POST['action'] === 'deleteCharge') {
            echo $controller->deleteCharge();
            exit();
        } elseif ($_POST['action'] === 'updateCharge') {
            echo $controller->updateCharge();
            exit();
        }
    } else {
        echo "Error: Action not specified.";
        exit();
    }
}

class ControllerCharge {
    public function addCharge() {
        $charge_type = isset($_POST['charge_type']) ? implode(', ', (array)$_POST['charge_type']) : '';
        $payment_method = isset($_POST['payment_method']) ? implode(', ', (array)$_POST['payment_method']) : '';
        $amount = floatval($_POST['amount'] ?? 0);
        $charge_date = $_POST['charge_date'] ?? '';
        $reception_date = $_POST['reception_date'] ?? '';
        $date_versement = $_POST['date_versement'] ?? '';

        $errors = [];

        if (empty($charge_type)) $errors[] = "Type de charge requis.";
        if ($amount <= 0) $errors[] = "Le montant doit être supérieur à zéro.";
        if (empty($charge_date)) $errors[] = "Date de charge requise.";
        if (empty($reception_date)) $errors[] = "Date de réception requise.";
        if (empty($date_versement)) $errors[] = "Date de versement requise.";
        if (empty($payment_method)) $errors[] = "Méthode de paiement requise.";

        if (!empty($errors)) {
            return "<p style='color:red'>" . implode('<br>', $errors) . "</p>";
        }

        $charge = new Charge($charge_type, $amount, $charge_date, $date_versement, $reception_date, $payment_method);

        if ($charge->save()) {
            return "<p style='color:green'>Charge ajoutée avec succès !</p>";
        } else {
            return "<p style='color:red'>Erreur lors de l'ajout de la charge.</p>";
        }
    }

    public function updateCharge() {
        $id = $_POST['id'] ?? null;
        $charge_type = isset($_POST['charge_type']) ? implode(', ', (array)$_POST['charge_type']) : '';
        $payment_method = isset($_POST['payment_method']) ? implode(', ', (array)$_POST['payment_method']) : '';
        $amount = floatval($_POST['amount'] ?? 0);
        $charge_date = $_POST['charge_date'] ?? '';
        $reception_date = $_POST['reception_date'] ?? '';
        $date_versement = $_POST['date_versement'] ?? '';

        if (!$id || empty($charge_type) || $amount <= 0 || empty($charge_date) || empty($reception_date) || empty($date_versement) || empty($payment_method)) {
            return json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs valides.']);
        }

        if (Charge::update($id, $charge_type, $amount, $charge_date, $date_versement, $reception_date, $payment_method)) {
            return json_encode(['status' => 'success', 'message' => 'Charge mise à jour.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function deleteCharge() {
        header('Content-Type: application/json');

        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            return;
        }

        $id = $_POST['id'];
        if (Charge::delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Charge supprimée avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
        }
    }

    public function listCharges() {
        return Charge::getAll();
    }
}
?>
  