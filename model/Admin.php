<?php
include_once __DIR__ . '/../database.php';

class Admin {
    private $id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $address;
    private $country;
    private $state;
    private $city;
    private $postal_code;
    private $password; // hashed password
    private $image;
    private $provider;
    private $provider_id;

    // Constructor with optional $id for updates
    public function __construct(
        $first_name,
        $last_name,
        $email,
        $phone = null,
        $address = null,
        $country = null,
        $state = null,
        $city = null,
        $postal_code = null,
        $password = null,
        $image = null,
        $provider = 'manual',
        $provider_id = null,
        $id = null
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->country = $country;
        $this->state = $state;
        $this->city = $city;
        $this->postal_code = $postal_code;
        $this->password = $password;
        $this->image = $image;
        $this->provider = $provider;
        $this->provider_id = $provider_id;
    }

    // Insert a new admin in DB
    public function register() {
        $conn = config::getConnexion();
        $sql = "INSERT INTO admin (
            first_name, last_name, email, phone, address, country, state, city, postal_code, password, image, provider, provider_id
        ) VALUES (
            :first_name, :last_name, :email, :phone, :address, :country, :state, :city, :postal_code, :password, :image, :provider, :provider_id
        )";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':country', $this->country);
            $stmt->bindParam(':state', $this->state);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':postal_code', $this->postal_code);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':provider', $this->provider);
            $stmt->bindParam(':provider_id', $this->provider_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Admin register error: " . $e->getMessage());
            return false;
        }
    }

    // Fetch all admins
    public static function getAll() {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM admin");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch one admin by ID
    public static function getAdminById($id) {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM admin WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update existing admin by id
    public function updateAdmin($id) {
        $conn = config::getConnexion();
        $sql = "UPDATE admin SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    country = :country,
                    state = :state,
                    city = :city,
                    postal_code = :postal_code,
                    password = :password,
                    image = :image,
                    provider = :provider,
                    provider_id = :provider_id
                WHERE id = :id";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':country', $this->country);
            $stmt->bindParam(':state', $this->state);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':postal_code', $this->postal_code);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':provider', $this->provider);
            $stmt->bindParam(':provider_id', $this->provider_id);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Admin update error: " . print_r($errorInfo, true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Admin update error: " . $e->getMessage());
            return false;
        }
    }

    // Delete admin by ID
    public static function deleteAdmin($id) {
        $conn = config::getConnexion();
        try {
            $stmt = $conn->prepare("DELETE FROM admin WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Admin delete error: " . $e->getMessage());
            return false;
        }
    }

    // Getter for ID
    public function getId() {
        return $this->id;
    }
}
?>
