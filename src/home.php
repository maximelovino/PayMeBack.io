<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'DBConnection.php';
require_once 'DataValidator.php';

if (!isset($_SESSION['username'])) {
	header("location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
</head>
<body>
<?php
include "navbar.html";
$eventsCount = count(DBConnection::getInstance()->getAllEventsForUser($_SESSION['username']));
$balanceByCurrency = DBConnection::getInstance()->getTotalBalanceForUser($_SESSION['username']);
$currencies = DBConnection::getInstance()->getAllCurrencies();
?>
<div class="container mt-5">
    <div class="container mt-5" id="content">
        <h1 class="display-3">Welcome to PayMeBack.io</h1>
		<?php if ($eventsCount > 0): ?>
            <p class="lead">You have <a
                        href="events.php"><?php echo $eventsCount . ' event' . ($eventsCount > 1 ? 's' : ''); ?></a> for
                a
                total balance of:
            </p>
            <div class="row">
                <div class="col-auto">
                    <p>View total as <select id="currencySelector" class="custom-select"><?php
							foreach ($currencies as $currency)
								echo '<option data-round="' . $currency['rounding_multiple'] . '" value="' . $currency['currency_code'] . '">' . $currency['full_name'] . '</option>';
							?> </select></p>
                </div>

                <div class="col">
                    <ul class="list-group">
						<?php
						foreach ($balanceByCurrency as $key => $value) {
							if ($value != 0) {
								$classToAdd = $value < 0 ? ' text-danger' : ' text-success';
								echo '<li class="list-group-item"><div class="row"><div id="' . $key . '" class="balanceAmount col' . $classToAdd . '">' . $value . '</div><div class="col-auto">' . $key . '</div></div></li>';
							}
						}
						?>
                    </ul>
                    <div class="row h4 mt-2">
                        <div class="col">Total</div>
                        <div id="singleCurrencyAmountPlaceHolder" class="col-auto"></div>
                    </div>
                </div>
            </div>
		<?php else: ?>
            <p class="lead">You don't have any events at the moment, create some events. <a href="events.php">Go to
                    events page</a></p>
		<?php endif; ?>
    </div>
</div>
<?php
include "footer.html"
?>
<script type="text/javascript">
    $("#homeLink").toggleClass("active");

    const currencySelect = document.querySelector('#currencySelector');
    const amountPlaceHolder = document.querySelector('#singleCurrencyAmountPlaceHolder');

    function balanceInOneCurrency() {
        const amounts = Array.from(document.querySelectorAll('.balanceAmount'));
        const destCurrency = currencySelect.value;
        const rounding = parseFloat(currencySelect.options[currencySelect.selectedIndex].dataset.round);
        console.log(rounding);
        let sum = 0;
        let calls = 0;
        amountPlaceHolder.innerText = `${sum.toFixed(2)} ${destCurrency}`;
        amounts.forEach((amountDiv) => {
            const amount = parseFloat(amountDiv.innerText);
            const currency = amountDiv.getAttribute('id');
            if (currency == destCurrency) {
                sum += amount;
                sum = Math.ceil(sum / rounding) * rounding;
                amountPlaceHolder.innerText = `${sum.toFixed(2)} ${destCurrency}`;
            } else {
                let request = new Request(`http://api.fixer.io/latest?base=${currency}&symbols=${destCurrency}`);
                calls++;
                fetch(request).then((response) => response.json()).then((res) => {
                    console.log(res);
                    const rate = parseFloat(res.rates[destCurrency]);
                    const newAmount = amount * rate;
                    console.log(amount, rate, newAmount);
                    sum += newAmount;
                    sum = Math.ceil(sum / rounding) * rounding;
                    calls--;
                    if (calls === 0) amountPlaceHolder.innerText = `${sum.toFixed(2)} ${destCurrency}`;
                });
            }
        });
    }

    currencySelect.onchange = balanceInOneCurrency;

    balanceInOneCurrency();
</script>

</body>
