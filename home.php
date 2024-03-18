<?php
session_start();

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

$base_url = "https://pd.$region.a.pvp.net/store/v1/wallet/$userid";
$credentials = [
    "entitlement_token" => $entitlements,
    "auth_token" => $accesstoken
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $base_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Riot-Entitlements-JWT: " . $entitlements,
    "Authorization: Bearer " . $accesstoken
]);

$response = curl_exec($ch);
if ($response === false) {
    echo 'Error: ' . curl_error($ch);
}


function fetchCurrencyInfo($currencyUuid)
{
    $currencyInfoUrl = "https://valorant-api.com/v1/currencies/$currencyUuid";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $currencyInfoUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response === false) {
        return [
            'uuid' => $currencyUuid,
            'name' => 'Unknown',
            'symbol' => 'Unknown',
            'displayIcon' => ''
        ];
    }

    curl_close($ch);

    $currencyInfo = json_decode($response, true);

    if (isset($currencyInfo['data'])) {
        $name = $currencyInfo['data']['displayName'];
        $symbol = $currencyInfo['data']['displayNameSingular'];
        $displayIcon = $currencyInfo['data']['displayIcon'];
        return [
            'uuid' => $currencyUuid,
            'name' => $name,
            'symbol' => $symbol,
            'displayIcon' => $displayIcon
        ];
    } else {
        return [
            'uuid' => $currencyUuid,
            'name' => 'Unknown',
            'symbol' => 'Unknown',
            'displayIcon' => ''
        ];
    }
}


curl_close($ch);

$data = json_decode($response, true);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>frxSai - Market offers</title>
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/market.css" rel="stylesheet">
    <link href="/css/card.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</head>

<body class="antialiased text-hero">


    <div class="page-wrapper">

        <div class="navigation-card-hero">
            <div class="navigation-card-hero-element">
                <span class="expen-icon" id="menuIcon_show">
                    <ion-icon name="eye-outline"></ion-icon>
                </span>
            </div>
        </div>

        <div class="navigation-card-hero-nav-hide" id="unhide">
            <div class="navigation-card-hero-nav">
                <div>
                    <?php
                    if (is_array($_SESSION['riot_response'])) {
                        foreach ($_SESSION['riot_response'] as $key => $value) {
                            if ($key === 'Name') {
                                echo "<span class='name-style'></span> <span class='name-value-style name-nav-hide'>$value</span>";
                            }
                        }
                    } else {
                        echo "No session data available.";
                    }
                    ?>
                    <span class="logout-hero-nav-hide">
                        <a href="#" onclick="document.getElementById('signoutForm').submit();">
                            Logout
                            <form id="signoutForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="signout">
                            </form>
                        </a>
                    </span>
                </div>
                <div>
                    <div>

                        <?php
                        if (isset($data['Balances'])) {
                            foreach ($data['Balances'] as $currencyUuid => $balance) {
                                $currencyInfo = fetchCurrencyInfo($currencyUuid);
                                echo "<div class='wallet-hero-ctn-nav-hide'>
                                        <div><img src='{$currencyInfo['displayIcon']}' alt='{$currencyInfo['name']}' class='img-balance-hero'></div>
                                        <div><span>$balance</span></div>
                                      </div>";
                            }
                        ?>

                        <?php
                        } else {
                            echo "No Balances found in the response.";
                        }
                        ?>
                    </div>
                </div>
                <span class="expen-icon" id="menuIcon_hide">
                    <ion-icon name="eye-off-outline"></ion-icon>
                </span>
            </div>
        </div>


        <div class="navigation-card">

            <div>

            </div>

            <div>
                <a class="balance-hero">
                    <div class="balance-header-hero">

                        <?php
                        if (is_array($_SESSION['riot_response'])) {
                            foreach ($_SESSION['riot_response'] as $key => $value) {
                                if ($key === 'Name') {
                                    echo "<span class='name-style'></span> <span class='name-value-style'>$value</span>";
                                }
                            }
                        } else {
                            echo "No session data available.";
                        }
                        ?>

                        <span>|</span>

                        <span class="logout-hero-nav">
                            <a class="logout-text" href="#" onclick="document.getElementById('signoutForm').submit();">
                                Logout
                                <form id="signoutForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="signout">
                                </form>
                            </a>
                        </span>

                        <?php
                        if (isset($data['Balances'])) {
                            foreach ($data['Balances'] as $currencyUuid => $balance) {
                                $currencyInfo = fetchCurrencyInfo($currencyUuid);
                                echo "<div class='wallet-left'><img src='{$currencyInfo['displayIcon']}' alt='{$currencyInfo['name']}' class='img-balance-hero' ></div>";
                                echo "<div class='wallet-right'> <span>$balance</span> </div>";
                            }
                        ?>

                        <?php
                        } else {
                            echo "No Balances found in the response.";
                        }
                        ?>
                    </div>
                </a>
            </div>

        </div>


        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 ctn-hero">

                <div class="container-title-hero-offers border-under">

                </div>

                <div class="container-title-hero-offers title-top-msg-ctn">
                    <div>
                        <span class="ion-icon-title">
                            <ion-icon name="flash-outline"></ion-icon>
                        </span>
                    </div>
                    <div>
                        <span class="large-title-hero">
                            Your Valorant Daily Offers
                        </span>
                    </div>
                </div>

                <div>
                    <?php require 'offer.php'; ?>
                </div>

                <div class="middle-main-hero">
                    <div class="middle-hero">
                        <div class="middle-ctn-with-slide hero-a1d">


                            <div class="middle-sub content-1 active">
                                <div class="middle-icon-text">
                                    <div class="icon-large">
                                        <ion-icon name="cube-outline"></ion-icon>
                                    </div>
                                    <div>
                                        <h1>Web Privacy</h1>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="text-large">
                                        <span>Server-side data stored:</span><br>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>No database</span>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>No username and password stored</span>
                                    </div>
                                </div>
                            </div>

                            <div class="middle-sub content-2">
                                <div class="middle-icon-text">
                                    <div class="icon-large">
                                        <ion-icon name="flask-outline"></ion-icon>
                                    </div>
                                    <div>
                                        <h1>Features</h1>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="text-large">
                                        <span>I'm using API to get your data:</span><br>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>Fetch your in-game balance and name</span>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>Fetch your current offers store in Valorant</span>
                                    </div>
                                </div>
                            </div>

                            <div class="middle-sub content-3">
                                <div class="middle-icon-text">
                                    <div class="icon-large">
                                        <ion-icon name="ice-cream-outline"></ion-icon>
                                    </div>
                                    <div>
                                        <h1>Donate</h1>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="text-large">
                                        <span>More money more features:</span><br>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>In-game inventory</span>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>Inventory-simulator</span>
                                    </div>
                                </div>
                                <div class="middle-icon-text">
                                    <div class="icon-small">
                                        <ion-icon name="ellipse"></ion-icon>
                                    </div>
                                    <div class="text-small">
                                        <span>Skin-Inspecter</span>
                                    </div>
                                </div>
                            </div>


                            <div class="middle-ctn-bottom">
                                <span class="icon-cur"><ion-icon name="chevron-back-outline"></ion-icon></span><span>1/3</span><span class="icon-cur"><ion-icon name="chevron-forward-outline"></ion-icon></span>
                            </div>

                        </div>
                    </div>
                    <div>
                        <div>
                            <iframe src="https://discord.com/widget?id=1218508353693683722&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
                        </div>
                    </div>
                </div>
                <div class="footer-ctn-hero">
                    <?php require 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>




    <script>
        const slides = document.querySelectorAll('.middle-sub');
        const prev = document.querySelector('.middle-ctn-bottom span:first-child');
        const next = document.querySelector('.middle-ctn-bottom span:last-child');
        const currentSlide = document.querySelector('.middle-ctn-bottom span:nth-child(2)');
        let currentIndex = 0;
        let autoSlideInterval;

        function showSlide(n) {
            slides.forEach((slide, index) => {
                if (index === n) {
                    slide.style.opacity = 0;
                    slide.classList.add('active');
                    setTimeout(() => {
                        slide.style.transition = 'opacity 0.5s ease-in-out';
                        slide.style.opacity = 1;
                    }, 50);
                    currentIndex = n;
                    currentSlide.textContent = `${n + 1}/${slides.length}`;
                } else {
                    slide.style.transition = 'opacity 0.5s ease-in-out';
                    slide.style.opacity = 0;
                    slide.classList.remove('active');
                }
            });
        }

        prev.addEventListener('click', () => {
            currentIndex === 0 ? showSlide(slides.length - 1) : showSlide(currentIndex - 1);
            clearInterval(autoSlideInterval);
        });

        next.addEventListener('click', () => {
            currentIndex === slides.length - 1 ? showSlide(0) : showSlide(currentIndex + 1);
            clearInterval(autoSlideInterval);
        });

        function autoSlide() {
            autoSlideInterval = setInterval(() => {
                currentIndex === slides.length - 1 ? showSlide(0) : showSlide(currentIndex + 1);
            }, 4000);
        }

        showSlide(0);
        autoSlide();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var menuIcon = document.getElementById('menuIcon_show');
            var navigation = document.getElementById('unhide');

            menuIcon.addEventListener('click', function() {
                navigation.classList.toggle('unhide');
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var menuIcon = document.getElementById('menuIcon_hide');
            var navigation = document.getElementById('unhide');

            menuIcon.addEventListener('click', function() {
                navigation.classList.toggle('unhide');
            });
        });
    </script>

</body>

</html>