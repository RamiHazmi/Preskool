<?php
include_once __DIR__ . '/../database.php';

class Level {
    public string $id;
    public string $name;
    public int $number_of_seances;
    public int $number_of_students = 0;
    public int $number_of_classes = 0;
    
    public function __construct($name, $number_of_seances) {
        $this->id = $this->generateId();
        $this->name = $name;
        $this->number_of_seances = $number_of_seances;
    }

    private function generateId(): string {
        return 'L' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            // Don't insert number_of_students or number_of_classes, they are computed dynamically
            $stmt = $pdo->prepare("INSERT INTO level (id, name, number_of_seances) VALUES (?, ?, ?)");
            return $stmt->execute([$this->id, $this->name, $this->number_of_seances]);
        } catch (PDOException $e) {
            error_log("Error in Level::save - " . $e->getMessage());
            return false;
        }
    }

    public static function delete($id) {
        try {
            $pdo = config::getConnexion();
            $stmt1 = $pdo->prepare("DELETE FROM class WHERE level_id = ?");
            $stmt1->execute([$id]);
    
            // Then delete the level itself
            $stmt2 = $pdo->prepare("DELETE FROM level WHERE id = ?");
            return $stmt2->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Level::delete - " . $e->getMessage());
            return false;
        }
    }

    public static function update($id, $name, $number_of_seances) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE level SET name = ?, number_of_seances = ? WHERE id = ?");
            return $stmt->execute([$name, $number_of_seances, $id]);
        } catch (PDOException $e) {
            error_log("Error in Level::update - " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("
                SELECT 
                    l.*,
                    -- Count classes per level
                    (SELECT COUNT(*) FROM class c WHERE c.level_id = l.id) AS number_of_classes,
                    -- Count students in all classes of this level
                    (
                        SELECT COUNT(*) 
                        FROM student s
                        JOIN class c ON s.class_id = c.id
                        WHERE c.level_id = l.id
                    ) AS number_of_students
                FROM level l
                ORDER BY l.id ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Level::getAll - " . $e->getMessage());
            return [];
        }
    }
    
    
}
