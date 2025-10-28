<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../model/PresenceModel.php';

class PresenceController {
    public function __construct() {}

    // Existing method to get presence list by emploi
    public function getPresencesByEmploi($id_emploi) {
        PresenceModel::initPresenceForEmploi($id_emploi);

        $presences = PresenceModel::getByEmploi($id_emploi);
        usort($presences, function ($a, $b) {
            return strcmp($a['username'], $b['username']);
        });

        header('Content-Type: application/json');
        echo json_encode($presences);
        exit;
    }

    // New method to get presence history by student ID
    public function getPresenceHistoryByStudent($studentId) {
        $history = PresenceModel::getPresenceHistoryByStudent($studentId);

        header('Content-Type: application/json');
        echo json_encode($history);
        exit;
    }

    // Update presence status
    public function updatePresence($id_emploi, $id_student, $status) {
        return PresenceModel::updateStatus($id_emploi, $id_student, $status);
    }

    // Handle HTTP requests
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['id_emploi'])) {
                $this->getPresencesByEmploi($_GET['id_emploi']);
            } elseif (isset($_GET['student_id'])) {
                $this->getPresenceHistoryByStudent($_GET['student_id']);
            } elseif (isset($_GET['from']) && isset($_GET['to'])) {
                // Handle presence/absence count for date interval
                header('Content-Type: application/json');
    
                $from = $_GET['from'];
                $to = $_GET['to'];
    
                // (Optional) validate dates format here if you want
    
                $presenceCount = $this->countPresenceBetweenDates($from, $to);
                $absenceCount = $this->countAbsenceBetweenDates($from, $to);
    
                echo json_encode([
                    'presenceCount' => $presenceCount,
                    'absenceCount' => $absenceCount
                ]);
                exit;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            if ($action === 'updatePresence') {
                header('Content-Type: application/json');
    
                $id_emploi = $_POST['id_emploi'] ?? '';
                $id_student = $_POST['id_student'] ?? '';
                $status = $_POST['status'] ?? '';
    
                if (empty($id_emploi) || empty($id_student) || empty($status)) {
                    echo json_encode(['status' => 'error', 'message' => 'Champs manquants.']);
                    exit;
                }
    
                try {
                    $result = $this->updatePresence($id_emploi, $id_student, $status);
                    if ($result) {
                        echo json_encode(['status' => 'success']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => "La mise à jour a échoué. Vérifiez si la présence existe."]);
                    }
                } catch (Exception $e) {
                    error_log("Erreur updatePresence: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur: ' . $e->getMessage()]);
                }
                exit;
            }
        }
    }
    
    public function getPresenceForWeek($startDate)
    {
        try {
            $conn = config::getConnexion();
            $sql = "SELECT * FROM presence WHERE DATE(presence_date) BETWEEN :startDate AND DATE_ADD(:startDate, INTERVAL 6 DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            error_log("getPresenceForWeek results count: " . count($results));
            error_log("Sample data: " . print_r(array_slice($results, 0, 3), true));
    
            return $results;
        } catch (PDOException $e) {
            error_log("getPresenceForWeek error: " . $e->getMessage());
            return [];
        }
    }
    public function countPresenceForWeek($startDate): int {
        try {
            $conn = config::getConnexion();
            $sql = "SELECT COUNT(*) FROM presence 
                    WHERE DATE(presence_date) BETWEEN :startDate AND DATE_ADD(:startDate, INTERVAL 6 DAY) 
                    AND LOWER(TRIM(status)) = 'present'";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("countPresenceForWeek error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function countPresenceBetweenDates($from, $to) {
        header('Content-Type: application/json');

        $conn = config::getConnexion();
        $sql = "SELECT COUNT(*) FROM presence WHERE status = 'Present' AND presence_date BETWEEN :from AND :to";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        return (int)$stmt->fetchColumn();
    }
    
    public function countAbsenceBetweenDates($from, $to) {
        header('Content-Type: application/json');

        $conn = config::getConnexion();
        $sql = "SELECT COUNT(*) FROM presence WHERE status = 'Absent' AND presence_date BETWEEN :from AND :to";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        return (int)$stmt->fetchColumn();
    }
    
    public function getPresenceCountBySubjectInClass($classId) {
        $conn = config::getConnexion();
    
        $sql = "SELECT 
                    m.nom AS subject_name,
                    COUNT(p.id_presence) AS presence_count
                FROM presence p
                JOIN emploi e ON p.id_emploi = e.id_emploi
                JOIN matiere m ON e.id_matiere = m.id_matiere
                WHERE e.id_classe = :classId AND p.status = 'present'
                GROUP BY m.nom";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute(['classId' => $classId]);
        return $stmt->fetchAll();
    }
    
    
    
    
    

}

// Handle any request made to presencecontroller.php
$controller = new PresenceController();
$controller->handleRequest();
?>
