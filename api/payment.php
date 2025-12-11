<?php
session_start();
require_once '../config/database.php';
require_once '../vendor/autoload.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Initialize PayPal client
function getPayPalClient() {
    global $paypalClientId, $paypalClientSecret, $paypalEnvironment;

    if ($paypalEnvironment === 'sandbox') {
        $environment = new SandboxEnvironment($paypalClientId, $paypalClientSecret);
    } else {
        $environment = new ProductionEnvironment($paypalClientId, $paypalClientSecret);
    }

    return new PayPalHttpClient($environment);
}

// Get full base URL for PayPal redirects
function getFullBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = $GLOBALS['baseUrl'] ?? '/Car-Parking-Rental';

    return $protocol . '://' . $host . $basePath;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_order') {
        // Create PayPal order
        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            echo json_encode(['success' => false, 'message' => 'Booking ID required']);
            exit;
        }

        // Get booking details
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $bookingId, 'user_id' => $_SESSION['user_id']]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit;
        }

        if ($booking['status'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Booking already processed']);
            exit;
        }

        try {
            $client = getPayPalClient();
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');

            $request->body = [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "reference_id" => $bookingId,
                    "amount" => [
                        "value" => number_format($booking['amount'], 0, '.', ''),
                        "currency_code" => "PHP",
                        "breakdown" => [
                            "item_total" => [
                                "value" => number_format($booking['amount'], 0, '.', ''),
                                "currency_code" => "PHP"
                            ],
                            "shipping" => [
                                "value" => "0.00",
                                "currency_code" => "PHP"
                            ],
                            "tax_total" => [
                                "value" => "0.00",
                                "currency_code" => "PHP"
                            ]
                        ]
                    ],
                    "items" => [[
                        "name" => "Parking Slot Booking",
                        "quantity" => "1",
                        "unit_amount" => [
                            "value" => number_format($booking['amount'], 0, '.', ''),
                            "currency_code" => "PHP"
                        ]
                    ]],
                    "description" => "Parking Slot Booking - " . $booking['id']
                ]],
                "application_context" => [
                    "cancel_url" => getFullBaseUrl() . "/index.php?page=booking&id=" . $booking['slot_id'] . "&status=cancelled",
                    "return_url" => getFullBaseUrl() . "/index.php?page=booking&id=" . $booking['slot_id'] . "&status=success&booking_id=" . $bookingId
                ]
            ];

            $response = $client->execute($request);

            // Store PayPal order ID in booking
            $stmt = $pdo->prepare("UPDATE bookings SET receipt = :paypal_order_id WHERE id = :id");
            $stmt->execute([
                'paypal_order_id' => $response->result->id,
                'id' => $bookingId
            ]);

            echo json_encode([
                'success' => true,
                'order_id' => $response->result->id,
                'approval_url' => $response->result->links[1]->href // Usually the approve link
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to create PayPal order: ' . $e->getMessage()]);
        }

    } elseif ($action === 'capture_order') {
        // Capture PayPal order
        $orderId = $_POST['order_id'] ?? null;
        $bookingId = $_POST['booking_id'] ?? null;

        if (!$orderId || !$bookingId) {
            echo json_encode(['success' => false, 'message' => 'Order ID and Booking ID required']);
            exit;
        }

        // Verify booking belongs to user
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id AND user_id = :user_id AND receipt = :order_id");
        $stmt->execute(['id' => $bookingId, 'user_id' => $_SESSION['user_id'], 'order_id' => $orderId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking or order']);
            exit;
        }

        try {
            $client = getPayPalClient();
            $request = new OrdersCaptureRequest($orderId);
            $response = $client->execute($request);

            if ($response->result->status === 'COMPLETED') {
                // Update booking status
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'active', paid_at = NOW(), payment_method = 'paypal' WHERE id = :id");
                $stmt->execute(['id' => $bookingId]);

                echo json_encode(['success' => true, 'message' => 'Payment completed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Payment not completed']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to capture payment: ' . $e->getMessage()]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>