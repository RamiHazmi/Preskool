<?php
include_once __DIR__ . '/../database.php';

class ClassModel {
    public string $id;
    public string $class_name;
    public string $level_id;
    public int $number_of_students;
    public int $year;
    public string $created_at;

    public function __construct(string $class_name, string $level_id) {
        $this->id = $this->generateId();
        $this->class_name = $class_name;
        $this->level_id = $level_id;
        $this->number_of_students = 0; // default
        $this->year = intval(date('Y')); // current year
        $this->created_at = date('Y-m-d H:i:s'); // current timestamp
    }

    private function generateId(): string {
        // Generate ID starting with 'C' + 6 random digits, e.g., C123456
        return 'C' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare(
                "INSERT INTO class (id, class_name, level_id, number_of_students, year, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $this->id,
                $this->class_name,
                $this->level_id,
                $this->number_of_students,
                $this->year,
                $this->created_at
            ]);
        } catch (PDOException $e) {
            error_log("Error in ClassModel::save - " . $e->getMessage());
            return false;
        }
    }

    public static function delete(string $id): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM class WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in ClassModel::delete - " . $e->getMessage());
            return false;
        }
    }

    public static function update(string $id, string $class_name, string $level_id): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE class SET class_name = ?, level_id = ? WHERE id = ?");
            return $stmt->execute([$class_name, $level_id, $id]);
        } catch (PDOException $e) {
            error_log("Error in ClassModel::update - " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("
                SELECT 
                    c.*, 
                    (
                        SELECT COUNT(*) 
                        FROM student s 
                        WHERE s.class_id = c.id
                    ) AS number_of_students
                FROM class c
                ORDER BY c.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in ClassModel::getAll - " . $e->getMessage());
            return [];
        }
    }
    
    
 
}
