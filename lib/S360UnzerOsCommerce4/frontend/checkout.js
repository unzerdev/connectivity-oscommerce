/** load unzer js; prevent overriding of window.checkout **/
let checkout = window.checkout;
let script = document.createElement('script');
script.src = 'https://static.unzer.com/v1/checkout.js';
script.onload = function () {
    window.unzerCheckout = window.checkout;
    window.checkout = checkout;
};

document.head.appendChild(script);

let selectedUnzerPaymentMethod = false;

checkApplePay();
addEventListener();

const paymentMethodBlock = $("#payment_method")[0];
new MutationObserver(() => {
    if (!paymentMethodBlock.parentElement) {

        checkApplePay();
        addEventListener();

        if (selectedUnzerPaymentMethod) {
            const selector = `input[name="payment"][data-unzer-payment-method="${selectedUnzerPaymentMethod}"]`;
            const radioElement = document.querySelector(selector);

            if (radioElement) {
                radioElement.checked = true;
            }
        }

    }
}).observe(paymentMethodBlock.parentElement, {childList: true});


// check if apple pay is enabled
function checkApplePay() {
    if (window.ApplePaySession && window.ApplePaySession.canMakePayments()) {
        let element = document.querySelector('.unzerLabel.applepay');
        if (element) {
            element.style.display = 'flex';
        }
    }
}

function addEventListener() {
    document.querySelectorAll('input[name="payment"]').forEach(radio => {
        radio.addEventListener('change', function () {
            selectedUnzerPaymentMethod = false;
            if (this.hasAttribute('data-unzer-payment-method')) {
                selectedUnzerPaymentMethod = this.getAttribute('data-unzer-payment-method');
            }
        });
    });
}

function displayErrorMessage(message) {
    let checkoutError = '<div class="messageBox"><strong>';
    checkoutError +=  message;
    checkoutError += '</strong></div>';
    alertMessage(checkoutError);
}

function s360_unzer_oscommerce4Callback() {
    let formData = new FormData();
    formData.append(
        "unzerPaymentMethod",
        selectedUnzerPaymentMethod
    );


    fetch(unzerCheckoutUrl, {
        method: "POST",
        body: formData
    })
        .then(function (response) {
            return response.text().then(function (text) {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error(text);
                }
            });
        })
        .then(function (data) {
            if (data.success) {
                let unzerCheckout = new window.unzerCheckout(data.ppg, {locale: unzerLocale});

                unzerCheckout.init().then(function () {
                    unzerCheckout.open();

                    unzerCheckout.abort(function () {
                        window.location.reload();
                    });

                    unzerCheckout.success(function (data) {
                        window.location.href = data.returnUrl;
                    });

                    unzerCheckout.error(function (error) {
                        displayErrorMessage(error.message);
                    });
                }).catch(function (error) {
                    displayErrorMessage(data);
                });
            } else {
                if(typeof data === 'string') {
                    displayErrorMessage(data);
                } else {
                    displayErrorMessage(data.messages.join("\n"));
                }
            }
        })
        .catch(function (error) {
            displayErrorMessage(error);
        });

    return true;
}