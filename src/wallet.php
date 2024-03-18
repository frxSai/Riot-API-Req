<?php
if (!isset($_SESSION['username']) || !isset($_SESSION['riot_response'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['signout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$entitlements = $_SESSION['riot_response']['Entitlements'];
$accesstoken = $_SESSION['riot_response']['Accesstoken'];
$userid = $_SESSION['riot_response']['Userid'];
$region = $_SESSION['riot_response']['Region'];




curl_close($ch);
$data = json_decode($response, true);
if (isset($data['Balances'])) {
    echo "<h2>Balances</h2>";
    echo "<ul>";
    foreach ($data['Balances'] as $currencyUuid => $balance) {
        $currencyInfo = fetchCurrencyInfo($currencyUuid);
        echo "<li><img src='{$currencyInfo['displayIcon']}' alt='{$currencyInfo['name']}' style='width: 20px; height: 20px;'> {$currencyInfo['name']}: $balance ({$currencyInfo['symbol']})</li>";
    }
    echo "</ul>";
} else {
    echo "No Balances found in the response.";
}
?>
