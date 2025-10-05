<?php
require_once "../../config/database.php"; // adjust path if needed

// Folder for slot images
$imageDir = "../../assets/images/parking_slots/";

// Ensure directory exists
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === "add") {
        // Auto-generate slot name
        $stmt = $pdo->query("SELECT COUNT(*) FROM slots");
        $slotCount = $stmt->fetchColumn();

        // Convert to letter (A=65)
        $slotLetter = chr(65 + $slotCount); 
        $slotName = "Slot " . $slotLetter;

        // Allow NULL for hourly/monthly if left blank
        $hourly_rate  = $_POST['hourly_rate'] === "" ? null : $_POST['hourly_rate'];
        $daily_rate   = $_POST['daily_rate'] === "" ? null : $_POST['daily_rate'];
        $monthly_rate = $_POST['monthly_rate'] === "" ? null : $_POST['monthly_rate'];
        $available    = isset($_POST['available']) ? 1 : 0;
        $imagePath    = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $fileName   = uniqid("slot_") . "_" . basename($_FILES['image']['name']);
            $targetPath = $imageDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = "assets/images/parking_slots/" . $fileName;
            }
        }

        // Insert slot
        $stmt = $pdo->prepare("INSERT INTO slots 
            (name, hourly_rate, daily_rate, monthly_rate, available, image) 
            VALUES (:name, :hourly_rate, :daily_rate, :monthly_rate, :available, :image)");

        $stmt->bindValue(':name', $slotName);
        $stmt->bindValue(':hourly_rate', $hourly_rate, $hourly_rate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':daily_rate', $daily_rate, $daily_rate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':monthly_rate', $monthly_rate, $monthly_rate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':available', $available, PDO::PARAM_INT);
        $stmt->bindValue(':image', $imagePath);
        $stmt->execute();

        reorderSlots($pdo); // keep names in order
        header("Location: ../../pages/admin/?current_page=slots");
        exit;

    } elseif ($action === "update") {
        $id = $_POST['id'];

        // Allow NULL for hourly/monthly if left blank
        $hourly_rate  = $_POST['hourly_rate'] === "" ? null : $_POST['hourly_rate'];
        $daily_rate   = $_POST['daily_rate'] === "" ? null : $_POST['daily_rate'];
        $monthly_rate = $_POST['monthly_rate'] === "" ? null : $_POST['monthly_rate'];
        $available    = isset($_POST['available']) ? 1 : 0;

        // Fetch existing slot
        $stmt = $pdo->prepare("SELECT image FROM slots WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $oldSlot = $stmt->fetch();

        $imagePath = $oldSlot['image'];

        // Handle image update
        if (!empty($_FILES['image']['name'])) {
            $fileName   = uniqid("slot_") . "_" . basename($_FILES['image']['name']);
            $targetPath = $imageDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = "assets/images/parking_slots/" . $fileName;
            }
        }

        // Update record
        $stmt = $pdo->prepare("UPDATE slots 
            SET hourly_rate=:hourly_rate, daily_rate=:daily_rate, monthly_rate=:monthly_rate, 
                available=:available, image=:image 
            WHERE id=:id");

        $stmt->bindValue(':hourly_rate', $hourly_rate, $hourly_rate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':daily_rate', $daily_rate, $daily_rate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':monthly_rate', $monthly_rate, $monthly_rate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':available', $available, PDO::PARAM_INT);
        $stmt->bindValue(':image', $imagePath);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        reorderSlots($pdo);
        header("Location: ../../pages/admin/?current_page=slots");
        exit;

    } elseif ($action === "delete") {
        $id = $_POST['id'];

        // Fetch image path before deleting
        $stmt = $pdo->prepare("SELECT image FROM slots WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $slot = $stmt->fetch();

        if ($slot && !empty($slot['image'])) {
            $filePath = "../../" . $slot['image']; // stored path is relative (assets/images/..)
            if (file_exists($filePath)) {
                unlink($filePath); // delete the image file
            }
        }

        // Delete slot from DB
        $stmt = $pdo->prepare("DELETE FROM slots WHERE id=:id");
        $stmt->execute([':id' => $id]);

        reorderSlots($pdo);
        header("Location: ../../pages/admin/?current_page=slots");
        exit;
    }
}

// Function to reorder and rename slots automatically
function reorderSlots($pdo) {
    $stmt = $pdo->query("SELECT id FROM slots ORDER BY id ASC");
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $i = 0;
    foreach ($slots as $slot) {
        $newName = "Slot " . chr(65 + $i); // 65 = 'A'
        $stmtUpdate = $pdo->prepare("UPDATE slots SET name = :name WHERE id = :id");
        $stmtUpdate->execute([
            ':name' => $newName,
            ':id' => $slot['id']
        ]);
        $i++;
    }
}
