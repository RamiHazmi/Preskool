<?php
include_once __DIR__ . '/../database.php';

class Matiere {
    public string $id;
    public string $nom;

    public function __construct($nom) {
        $this->id = $this->generateId();
        $this->nom = $nom;
    }

    private function generateId(): string {
        return 'M' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("INSERT INTO matiere (id_matiere, nom) VALUES (?, ?)");
            return $stmt->execute([$this->id, $this->nom]);
        } catch (PDOException $e) {
            error_log("Error in Matiere::save - " . $e->getMessage());
            return false;
        }
    }

    public static function delete($id) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM matiere WHERE id_matiere = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Matiere::delete - " . $e->getMessage());
            return false;
        }
    }

    public static function update($id, $nom) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE matiere SET nom = ? WHERE id_matiere = ?");
            return $stmt->execute([$nom, $id]);
        } catch (PDOException $e) {
            error_log("Error in Matiere::update - " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT id_matiere AS id, nom FROM matiere");
            $stmt->execute();
            $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $matieres ?: [];
        } catch (PDOException $e) {
            error_log("Error fetching matieres: " . $e->getMessage());
            return [];
        }
    }
}