<?php
include_once __DIR__ . '/../database.php';

class Emploi {
    public string $id_emploi;
    public string $id_matiere;
    public string $id_enseignant;
    public string $id_classe;
    public string $date_debut;
    public string $date_fin;
    public string $cour_type;
    public ?string $location_details; // New property for classroom or Meet link

    public function __construct($id_matiere, $id_enseignant, $id_classe, $date_debut, $date_fin, $cour_type, $location_details = null) {
        $this->id_emploi = $this->generateId();
        $this->id_matiere = $id_matiere;
        $this->id_enseignant = $id_enseignant;
        $this->id_classe = $id_classe;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->cour_type = $cour_type;
        $this->location_details = $location_details; // Initialize with optional value
    }

    private function generateId(): string {
        $randomNumbers = str_pad(strval(mt_rand(0, 999999)), 6, '0', STR_PAD_LEFT);
        return 'EM' . $randomNumbers;
    }

    public function save(): bool {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO emploi (id_emploi, id_matiere, id_enseignant, id_classe, date_debut, date_fin, cour_type, location_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $this->id_emploi,
            $this->id_matiere,
            $this->id_enseignant,
            $this->id_classe,
            $this->date_debut,
            $this->date_fin,
            $this->cour_type,
            $this->location_details
        ]);
    }

    public static function delete($id_emploi): bool {
        try {
            $pdo = config::getConnexion();
    
            // First: delete all related presence records
            $stmtPresence = $pdo->prepare("DELETE FROM presence WHERE id_emploi = :id_emploi");
            $stmtPresence->execute([':id_emploi' => $id_emploi]);
    
            // Then: delete the emploi itself
            $stmtEmploi = $pdo->prepare("DELETE FROM emploi WHERE id_emploi = :id_emploi");
            return $stmtEmploi->execute([':id_emploi' => $id_emploi]);
        } catch (PDOException $e) {
            error_log("Emploi::delete error: " . $e->getMessage());
            return false;
        }
    }
    

    public static function update($id, $id_matiere, $id_enseignant, $id_classe, $date_debut, $date_fin, $cour_type, $location_details): bool {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("UPDATE emploi SET id_matiere = ?, id_enseignant = ?, id_classe = ?, date_debut = ?, date_fin = ?, cour_type = ?, location_details = ? WHERE id_emploi = ?");
        return $stmt->execute([
            $id_matiere,
            $id_enseignant,
            $id_classe,
            $date_debut,
            $date_fin,
            $cour_type,
            $location_details,
            $id
        ]);
    }

    public static function getAll(): array {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("
            WITH RECURSIVE weeks(n) AS (
                SELECT 0
                UNION ALL
                SELECT n + 1 FROM weeks WHERE n < 4
            )
            SELECT 
                CONCAT(e.id_emploi, '_w', w.n + 1) AS emploi_instance,
                e.id_emploi,
                e.id_matiere,
                e.id_enseignant,
                e.id_classe,
                DATE_ADD(e.date_debut, INTERVAL w.n WEEK) AS date_debut,
                DATE_ADD(e.date_fin, INTERVAL w.n WEEK) AS date_fin,
                e.cour_type,
                e.location_details
            FROM emploi e
            JOIN weeks w ON 1
            ORDER BY e.id_emploi, w.n
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    
    public static function getConflicts(
        DateTime $start, 
        DateTime $end, 
        string $id_enseignant, 
        string $id_classe, 
        string $cour_type, 
        ?string $location_details
    ): array {
        $pdo = config::getConnexion();
    
        $sql = "SELECT * FROM emploi
                WHERE (
                    id_enseignant = :id_enseignant
                    OR id_classe = :id_classe
                    OR (
                        LOWER(cour_type) = 'présentiel' 
                        AND location_details = :location_details
                        
                    )
                )
                AND NOT (
                    date_fin <= :start_date OR
                    date_debut >= :end_date
                )";
    
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_enseignant', $id_enseignant);
        $stmt->bindValue(':id_classe', $id_classe);
        $stmt->bindValue(':location_details', $location_details ?? '');
        $stmt->bindValue(':start_date', $start->format('Y-m-d H:i:s'));
        $stmt->bindValue(':end_date', $end->format('Y-m-d H:i:s'));
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>