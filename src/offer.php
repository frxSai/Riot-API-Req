<?php

if (!isset($_SESSION['username']) || !isset($_SESSION['riot_response'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['riot_response']['Accesstoken'])) {
    echo json_encode(["error" => "Access token not found in session."]);
    exit();
}

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


<div class="card-container hero-screen">
    <?php
    $offers = $data['SkinsPanelLayout']['SingleItemStoreOffers'];
    $offerCount = count($offers);

    for ($i = 0; $i < $offerCount; $i++) {
        $offerID = $offers[$i]['OfferID'];
        $cost = $offers[$i]['Cost']['85ad13f7-3d1b-5128-9eb2-7cd8ee0b5741'];

        $offerDetails = getOfferDetails($offerID);

        if ($offerDetails !== null) {
            $displayName = $offerDetails['displayName'];
            $displayIcon = $offerDetails['displayIcon'];
    ?>


            <div class="card-ctn-hero">
                <div class="card">
                    <div class="content">
                        <div class="img-container">
                            <img src="<?php echo $displayIcon; ?>">
                        </div>
                        <div class="detail-hero">
                            <div class="text-detail-start-left">
                                <span><?php echo $displayName; ?></span><br>
                                <span>From</span><span class="cost"> VP <?php echo $cost; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>