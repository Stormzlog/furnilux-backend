<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


// ⚠️ DO NOT expose your live secret key in client-side code.
$secret_key = "sk_live_83e78e59d23816c2f400106c33e85455f1415b9c"; 

$email = $_POST['email'] ?? '';
$amount = $_POST['amount'] ?? 0; // amount in kobo (for NGN) or cents (for USD)
$currency = $_POST['currency'] ?? 'USD'; 

if (!$email || !$amount) {
  echo json_encode(['status' => false, 'message' => 'Missing parameters']);
  exit;
}

// Prepare data for Paystack API
$data = [
  'email' => $email,
  'amount' => intval($amount),
  'currency' => $currency,
];

// Initialize payment
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/initialize");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $secret_key",
  "Cache-Control: no-cache",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
if (curl_errno($ch)) {
  echo json_encode(['status' => false, 'message' => 'Curl error: ' . curl_error($ch)]);
  exit;
}
curl_close($ch);

// Return Paystack response (JSON)
echo $result;
?>

$url = "https://api.paystack.co/transaction/initialize";

$fields = [
  'email' => $email,
  'amount' => $amount,
  'currency' => $currency,
  'callback_url' => 'https://furnilux-backend.free.nf/verify.php'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $secret_key",
  "Cache-Control: no-cache"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

echo $result;
?>