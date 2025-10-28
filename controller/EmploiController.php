<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../model/Emploi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new EmploiController();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addEmploi':
                $controller->addEmploi();
                break;
                case 'deleteEmploi':
                    echo $controller->deleteEmploi();
                    break;
                case 'updateEmploi':
                    echo $controller->updateEmploi();
                    break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action non reconnue.']);
                exit();
        }
    }
}
class EmploiController {

// PHP - Controller method addEmploi()
public function addEmploi() {
    header('Content-Type: application/json');

    $id_matiere = $_POST['id_matiere'] ?? '';
    $id_enseignant = $_POST['id_enseignant'] ?? '';
    $id_classe = $_POST['id_classe'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $cour_type = $_POST['cour_type'] ?? '';
    $location_details = $_POST['location_details'] ?? null;

    if (empty($id_matiere) || empty($id_enseignant) || empty($id_classe) || empty($date_debut) || empty($date_fin) || empty($cour_type)) {
        echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']);
        return;
    }

    try {
        $startOriginal = new DateTime($date_debut);
        $endOriginal = new DateTime($date_fin);

        if ($startOriginal >= $endOriginal) {
            echo json_encode(['status' => 'error', 'message' => 'La date de début doit être avant la date de fin.']);
            return;
        }

        if ($startOriginal->format('D') === 'Sun' || $endOriginal->format('D') === 'Sun') {
            echo json_encode(['status' => 'error', 'message' => 'Les horaires ne peuvent pas être un dimanche.']);
            return;
        }

        $heureDebut = (int)$startOriginal->format('H');
        if ($heureDebut < 8) {
            echo json_encode(['status' => 'error', 'message' => 'L\'heure de début doit être après 8h00.']);
            return;
        }

        $heureFin = (int)$endOriginal->format('H');
        if ($heureFin > 18 || ($heureFin == 18 && (int)$endOriginal->format('i') > 0)) {
            echo json_encode(['status' => 'error', 'message' => 'L\'heure de fin doit être avant 18h00.']);
            return;
        }

        $successCount = 0;
        $errors = [];

        // Generate emploi every 7 days for 16 weeks (4 months)
        for ($i = 0; $i < 16; $i++) {
            $start = clone $startOriginal;
            $end = clone $endOriginal;
            $start->modify("+{$i} week");
            $end->modify("+{$i} week");

            // Conflict check
            $conflicts = Emploi::getConflicts($start, $end, $id_enseignant, $id_classe, $cour_type, $location_details);

            foreach ($conflicts as $conflict) {
                $conflictStart = new DateTime($conflict['date_debut']);
                $conflictEnd = new DateTime($conflict['date_fin']);

                if ($start < $conflictEnd && $conflictStart < $end) {
                    if ($conflictStart->format('Y-m-d') === $start->format('Y-m-d')) {
                        if (strtolower($cour_type) === 'présentiel' &&
                            strtolower($conflict['cour_type']) === 'présentiel' &&
                            $conflict['location_details'] === $location_details) {
                            $errors[] = "Conflit: Salle occupée le " . $start->format('Y-m-d H:i');
                            continue 2;
                        }
                    }
                    if ($conflict['id_enseignant'] === $id_enseignant) {
                        $errors[] = "Conflit: Enseignant occupé le " . $start->format('Y-m-d H:i');
                        continue 2;
                    }
                }
            }

            // Save emploi
            $emploi = new Emploi($id_matiere, $id_enseignant, $id_classe, $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $cour_type, $location_details);

            if ($emploi->save()) {
                $successCount++;
            } else {
                $errors[] = "Erreur lors de l'ajout pour le " . $start->format('Y-m-d');
            }
        }

        if ($successCount > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Emploi ajouté avec succès !']);

        
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Aucun emploi n\'a été ajouté.',
                'errors' => $errors
            ]);
        }
    } catch (Exception $e) {
        error_log("Add Emploi Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Une erreur s\'est produite. Consultez les logs pour plus de détails.']);
    }
}


    public function updateEmploi() {
        header('Content-Type: application/json');
    
        $id_emploi = $_POST['id_emploi'] ?? '';
        $id_matiere = $_POST['id_matiere'] ?? '';
        $id_enseignant = $_POST['id_enseignant'] ?? '';
        $id_classe = $_POST['id_classe'] ?? '';
        $date_debut = $_POST['date_debut'] ?? '';
        $date_fin = $_POST['date_fin'] ?? '';
        $cour_type = $_POST['cour_type'] ?? '';
        $location_details = $_POST['location_details'] ?? null;
    
        if (empty($id_emploi) || empty($id_matiere) || empty($id_enseignant) || empty($id_classe) || empty($date_debut) || empty($date_fin) || empty($cour_type)) {
            echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']);
            return;
        }
    
        try {
            $start = new DateTime($date_debut);
            $end = new DateTime($date_fin);
    
            if ($start >= $end) {
                echo json_encode(['status' => 'error', 'message' => 'La date de début doit être avant la date de fin.']);
                return;
            }
    
            if ($start->format('D') === 'Sun' || $end->format('D') === 'Sun') {
                echo json_encode(['status' => 'error', 'message' => 'Les horaires ne peuvent pas être un dimanche.']);
                return;
            }
    
            $heureDebut = (int)$start->format('H');
            if ($heureDebut < 8) {
                echo json_encode(['status' => 'error', 'message' => 'L\'heure de début doit être après 8h00.']);
                return;
            }
    
            $heureFin = (int)$end->format('H');
            if ($heureFin > 18 || ($heureFin == 18 && (int)$end->format('i') > 0)) {
                echo json_encode(['status' => 'error', 'message' => 'L\'heure de fin doit être avant 18h00.']);
                return;
            }
    
            $conflicts = Emploi::getConflicts($start, $end, $id_enseignant, $id_classe, $cour_type, $location_details);
    
            foreach ($conflicts as $conflict) {
                if ($conflict['id_emploi'] == $id_emploi) continue; // Ignore current emploi being updated
    
                $conflictStart = new DateTime($conflict['date_debut']);
                $conflictEnd = new DateTime($conflict['date_fin']);
    
                if ($start < $conflictEnd && $conflictStart < $end) {
                    if ($conflictStart->format('Y-m-d') === $start->format('Y-m-d')) {
                        if (strtolower($cour_type) === 'présentiel' 
                            && strtolower($conflict['cour_type']) === 'présentiel'
                            && $conflict['location_details'] === $location_details) {
                            echo json_encode(['status' => 'error', 'message' => 'Cette salle est déjà réservée à cette heure le même jour.']);
                            return;
                        }
    
                    }
    
                    if ($conflict['id_enseignant'] === $id_enseignant) {
                        echo json_encode(['status' => 'error', 'message' => 'Cet enseignant est déjà occupé pendant cet horaire.']);
                        return;
                    }
                }
            }
    
            if (Emploi::update($id_emploi, $id_matiere, $id_enseignant, $id_classe, $date_debut, $date_fin, $cour_type, $location_details)) {
                echo json_encode(['status' => 'success', 'message' => 'Emploi mis à jour avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Erreur lors de la mise à jour de l'emploi."]);
            }
    
        } catch (Exception $e) {
            error_log("Update Emploi Error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Une erreur s\'est produite. Consultez les logs pour plus de détails.']);
        }
    }
    
    // Supprimer un emploi
    public function deleteEmploi() {
        header('Content-Type: application/json');

        $id_emploi = $_POST['id_emploi'] ?? '';
        if (!$id_emploi) {
            echo json_encode(['status' => 'error', 'message' => "ID d'emploi manquant."]);
            return;
        }

        if (Emploi::delete($id_emploi)) {
            echo json_encode(['status' => 'success', 'message' => "Emploi supprimé avec succès."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Erreur lors de la suppression de l'emploi."]);
        }
    }

    public function listEmplois(): array {
        return Emploi::getAll();
    }
    public function getSubjectsByClass($classId) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT 
                    m.id_matiere, 
                    m.nom AS subject_name,
                    COUNT(e.id_emploi) AS study_count
                FROM emploi e
                JOIN matiere m ON e.id_matiere = m.id_matiere
                WHERE e.id_classe = :classId
                GROUP BY m.id_matiere, m.nom
                ORDER BY study_count DESC
            ");
            $stmt->execute(['classId' => $classId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching subjects by class: " . $e->getMessage());
            return [];
        }
    }
    
    
}
