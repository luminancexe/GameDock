<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? null;

if (!$id || !in_array($type, ['buy', 'rent'])) {
    die("Invalid receipt request.");
}

$data = null;
if ($type === 'buy') {
    $stmt = $pdo->prepare("SELECT p.*, g.title, g.image, u.fullname, u.email FROM purchases p JOIN games g ON p.game_id = g.game_id JOIN users u ON p.user_id = u.user_id WHERE p.purchase_id = ? AND p.user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("SELECT r.*, g.title, g.image, u.fullname, u.email FROM rentals r JOIN games g ON r.game_id = g.game_id JOIN users u ON r.user_id = u.user_id WHERE r.rental_id = ? AND r.user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$data) {
    die("Receipt not found or you do not have permission to view it.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - GameDock</title>
    <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@700&family=Hanken+Grotesk:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Hanken Grotesk', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            margin: 0;
            padding: 40px 20px;
        }
        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-family: 'Unbounded', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo svg { width: 32px; height: 32px; }
        .receipt-title {
            text-align: right;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
            font-weight: 600;
        }
        .receipt-title h1 {
            color: #111827;
            margin: 5px 0 0 0;
            font-size: 28px;
            font-family: 'Unbounded', sans-serif;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .detail-group h4 {
            margin: 0 0 5px 0;
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
        }
        .detail-group p {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .item-table th, .item-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .item-table th {
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
        }
        .item-table td { font-weight: 600; }
        .item-table td.total {
            text-align: right;
            font-size: 20px;
            color: #111827;
            font-family: 'Unbounded', sans-serif;
        }
        .print-btn-container {
            text-align: center;
            margin-top: 40px;
        }
        .btn-print {
            background-color: #E8A33D;
            color: #1A1204;
            border: none;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Hanken Grotesk', sans-serif;
            text-decoration: none;
        }
        .btn-print:hover { background-color: #F4B65A; }
        .btn-back {
            display: inline-block;
            margin-top: 15px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-back:hover { color: #111827; text-decoration: underline; }

        @media print {
            body { background: none; padding: 0; }
            .receipt-container { box-shadow: none; padding: 0; }
            .print-btn-container { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="header">
        <div class="logo">
            <svg viewBox="0 0 104 104" aria-hidden="true">
                <path d="M56 24 A28 28 0 1 0 56 80 L56 68" fill="none" stroke="#E8A33D" stroke-width="10" stroke-linecap="round"/>
                <line x1="70" y1="30" x2="70" y2="74" stroke="#4FD1D9" stroke-width="10" stroke-linecap="round"/>
                <path d="M70 30 A22 22 0 0 1 70 74" fill="none" stroke="#4FD1D9" stroke-width="10" stroke-linecap="round"/>
            </svg>
            GameDock
        </div>
        <div class="receipt-title">
            Receipt
            <h1>#<?php echo $type === 'buy' ? 'PUR-' . str_pad($data['purchase_id'], 5, '0', STR_PAD_LEFT) : 'RNT-' . str_pad($data['rental_id'], 5, '0', STR_PAD_LEFT); ?></h1>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-group">
            <h4>Billed To</h4>
            <p><?php echo htmlspecialchars($data['fullname']); ?></p>
            <p style="font-weight: 400; color: #6b7280;"><?php echo htmlspecialchars($data['email']); ?></p>
        </div>
        <div class="detail-group" style="text-align: right;">
            <h4>Date</h4>
            <p><?php echo date("F j, Y", strtotime($type === 'buy' ? $data['purchase_date'] : $data['start_date'])); ?></p>
            
            <h4 style="margin-top: 15px;">Payment Method</h4>
            <p>Credit Card (**** **** **** 4242)</p>
        </div>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="uploads/<?php echo htmlspecialchars($data['image']); ?>" alt="Cover" style="width: 40px; height: 50px; border-radius: 4px; object-fit: cover;">
                        <div>
                            <div style="font-size: 16px;"><?php echo htmlspecialchars($data['title']); ?></div>
                            <div style="font-size: 13px; color: #6b7280; font-weight: 400;">
                                <?php 
                                if ($type === 'buy') {
                                    echo "PC Digital Game";
                                } else {
                                    echo htmlspecialchars($data['console']) . " Rental (" . $data['rental_days'] . " days)<br>";
                                    echo date("M j", strtotime($data['start_date'])) . " - " . date("M j", strtotime($data['end_date']));
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="total">$<?php echo number_format($data['total_price'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center; color: #9ca3af; font-size: 14px; margin-top: 60px;">
        Thank you for your business!<br>
        GameDock Inc. | contact@gamedock.demo
    </div>

    <div class="print-btn-container">
        <button class="btn-print" onclick="window.print()">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Download PDF / Print
        </button>
        <br>
        <a href="profile.php" class="btn-back">&larr; Back to Profile</a>
    </div>
</div>

</body>
</html>
