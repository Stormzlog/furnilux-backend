<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ⚠️ Never expose your live secret key in any front-end code
$secret_key = "sk_live_83e78e59d23816c2f400106c33e85455f1415b9c"; 

$reference = $_GET['reference'] ?? '';

if (!$reference) {
  echo json_encode(['status' => false, 'message' => 'No transaction reference provided']);
  exit;
}

$url = "https://api.paystack.co/transaction/verify/" . urlencode($reference);

$ch = curl_init();
curl_setopt_array($ch, [
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer $secret_key",
    "Cache-Control: no-cache",
  ],
]);

$result = curl_exec($ch);

if (curl_errno($ch)) {
  echo json_encode(['status' => false, 'message' => 'cURL error: ' . curl_error($ch)]);
  curl_close($ch);
  exit;
}

curl_close($ch);

$response = json_decode($result, true);

// ✅ Check if Paystack confirms success
if (
  $response &&
  isset($response['data']['status']) &&
  $response['data']['status'] === 'success'
) {
  // Payment confirmed
  echo json_encode([
    'status' => true,
    'message' => 'Payment successful',
    'data' => $response['data']
  ]);
} else {
  // Payment failed or pending
  echo json_encode([
    'status' => false,
    'message' => $response['message'] ?? 'Payment verification failed',
    'data' => $response['data'] ?? []
  ]);
}
?>
