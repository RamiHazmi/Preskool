<?php
require_once __DIR__ . '/session_config.php'; 
include_once __DIR__ . '/../database.php';
include(__DIR__ . '/../model/societe.php');

$controller = new ControllerSociete();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'addSociete':
                echo $controller->addSociete();
                break;
            case 'updateSociete':
                echo $controller->updateSociete();
                break;
            case 'deleteSociete':
                echo $controller->deleteSociete($data);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action JSON inconnue.']);
        }
        exit;
    }

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addSociete':
                echo $controller->addSociete();
                break;
            case 'updateSociete':
                echo $controller->updateSociete();
                break;
            case 'deleteSociete':
                echo $controller->deleteSociete($_POST);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action POST inconnue.']);
        }
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Aucune action détectée.']);
}

class ControllerSociete {

    public function addSociete() {
        $errors = [];

        $nom = trim($_POST['nom_centre'] ?? '');
        $matricule = trim($_POST['matricule_fiscale'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['numero_telephone'] ?? '');

        if (!$nom) $errors[] = "Le nom du centre est requis.";
        if (!$matricule) $errors[] = "Le matricule fiscale est requis.";
        if (!$adresse) $errors[] = "L'adresse est requise.";
        if (!$telephone) $errors[] = "Le numéro de téléphone est requis.";

        // Handle image upload
        $imagePath = '';
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../view/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $imageName = uniqid("societe_", true) . "." . strtolower($extension);
            $targetPath = $uploadDir . $imageName;
            $relativePath = 'view/images/' . $imageName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                $imagePath = $relativePath;
            } else {
                $errors[] = "Erreur lors du téléchargement de l'image.";
            }
        }

        if (count($errors)) {
            return "<p style='color:red;'>" . implode("<br>", $errors) . "</p>";
        }

        $societe = new Societe($nom, $matricule, $adresse, $telephone, $imagePath);
        if ($societe->register()) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        
            // Always set these session vars
            $_SESSION['societe_nom'] = $nom;
            $_SESSION['societe_matricule'] = $matricule;
            $_SESSION['societe_adresse'] = $adresse;
            $_SESSION['societe_telephone'] = $telephone;
            $_SESSION['societe_id'] = $societe->getId();
            if ($imagePath) {
                $_SESSION['societe_logo'] = $imagePath;
            }
        
            return "<p style='color:green;'>Société ajoutée avec succès.</p>";
        } else {
            return "<p style='color:red;'>Erreur lors de l'enregistrement de la société.</p>";
        }
        
    }

    public function updateSociete() {
        $errors = [];
        $id = $_POST['id_societe'] ?? '';
        $nom = trim($_POST['nom_centre'] ?? '');
        $matricule = trim($_POST['matricule_fiscale'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['numero_telephone'] ?? '');
    
        if (!$id) $errors[] = "ID manquant.";
        if (!$nom) $errors[] = "Le nom du centre est requis.";
        if (!$matricule) $errors[] = "Le matricule fiscale est requis.";
        if (!$adresse) $errors[] = "L'adresse est requise.";
        if (!$telephone) $errors[] = "Le numéro de téléphone est requis.";
    
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT image_path FROM societe WHERE id_societe = ?");
        $stmt->execute([$id]);
        $existingImage = $stmt->fetchColumn();
        $imagePath = $existingImage;
    
        if (!empty($_FILES["image"]["name"])) {
            $uploadDir = __DIR__ . '/../view/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
            $imageName = uniqid("societe_", true) . "_" . basename($_FILES["image"]["name"]);
            $targetPath = $uploadDir . $imageName;
            $relativePath = 'view/images/' . $imageName;
    
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                if ($existingImage && file_exists(__DIR__ . '/../' . $existingImage)) {
                    unlink(__DIR__ . '/../' . $existingImage);
                }
                $imagePath = $relativePath;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image.";
            }
        }
    
        if (count($errors)) {
            return "<p style='color:red;'>" . implode("<br>", $errors) . "</p>";
        }
    
        $societe = new Societe($nom, $matricule, $adresse, $telephone, $imagePath, $id);
    
        $reflection = new ReflectionClass($societe);
        $prop = $reflection->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($societe, $id);
    
        if ($societe->update()) {
            // Update session variables here so your app shows the new info immediately
            
    
            $_SESSION['societe_nom'] = $nom;
            $_SESSION['societe_matricule'] = $matricule;
            $_SESSION['societe_adresse'] = $adresse;
            $_SESSION['societe_telephone'] = $telephone;
            $_SESSION['societe_logo'] = $imagePath;
    
            return "<p style='color:green;'>Société modifiée avec succès.</p>";
        } else {
            return "<p style='color:red;'>Erreur lors de la modification.</p>";
        }
    }
    

    public function deleteSociete($data) {
        if (!isset($data['id'])) {
            return json_encode(['status' => 'error', 'message' => 'ID requis.']);
        }
    
        $id = trim($data['id']);
        $deleted = Societe::delete($id);
    
        if ($deleted) {
           
            // Clear session variables related to the deleted company
            unset($_SESSION['societe_id']);
            unset($_SESSION['societe_nom']);
            unset($_SESSION['societe_matricule']);
            unset($_SESSION['societe_adresse']);
            unset($_SESSION['societe_telephone']);
            unset($_SESSION['societe_logo']);
        }
    
        return json_encode([
            'status' => $deleted ? 'success' : 'error',
            'message' => $deleted ? 'Société supprimée avec succès.' : 'Erreur lors de la suppression.'
        ]);
    }
    
    public function listSocietes() {
        return Societe::getAll();
    }

    public function getSocieteById($id) {
        return Societe::getById($id);
    }
}
?>