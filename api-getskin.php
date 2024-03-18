<?php
$url = "https://pd.$region.a.pvp.net/store/v2/storefront/$userid";

$headers = array(
    "X-Riot-Entitlements-JWT: $entitlements",
    "Authorization: Bearer $accesstoken"
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

if ($response === false) {
    echo json_encode(["error" => "Failed to fetch data from the API."]);
    exit();
}

$data = json_decode($response, true);

if ($data === null) {
    echo json_encode(["error" => "Failed to decode API response."]);
    exit();
}

function getOfferDetails($offerID)
{
    $api_url = "https://valorant-api.com/v1/weapons/skinlevels/$offerID";
    $api_response = file_get_contents($api_url);
    $api_data = json_decode($api_response, true);

    if ($api_data && isset($api_data['data']['displayName']) && isset($api_data['data']['displayIcon'])) {
        return array(
            'displayName' => $api_data['data']['displayName'],
            'displayIcon' => $api_data['data']['displayIcon']
        );
    }
    return null;
}

?>