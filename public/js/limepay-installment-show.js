_createCookie = function (cookieName, value, minutes) {
    if (minutes) {
        var date = new Date();
        date.setTime(date.getTime() + minutes * 60 * 1000);
        var expires = "; expires = " + date.toGMTString();
    } else {
        var expires = "";
    }

    document.cookie = cookieName + "=" + value + expires + "; path=/";
};

_accessCookie = function (cookieName) {
    var name = cookieName + '=';
    var allCookieArray = document.cookie.split(';');

    for (var i = 0; i < allCookieArray.length; i++) {
        var temp = allCookieArray[i].trim();
        if (temp.indexOf(name) == 0) return temp.substring(name.length, temp.length);
    }

    return '';
};

_deleteCookie = function (cookieName) {
    document.cookie = cookieName + '=; expires = Thu, 01 Jan 1970 00:00:01 GMT; path=/';
};

var minAmtLimit = parseFloat(_accessCookie('minAllowedAmt'));

jQuery( function($) {

    $(document).on('change', '#lpInstallmentSwitch', function() {
        if ($(this).closest('.lp-toggle-container').hasClass('payplan-disabled')) {
            $(this).prop('checked', false);
            var parentLabelElem = $(this).closest('.switch');
            parentLabelElem.addClass("disabled-swt");

            // Reset Toggle Animations;
            setTimeout(function() {
                parentLabelElem.removeClass("disabled-swt");
            }, 610);

            return false;
        }


        if ($(this).prop('checked')) {
            _createCookie('lp-preferred-bnpl-option', '1', 120); /* Set cookie for 2 hours */
            $('.lp-toggle-container .payment-type').removeClass('active');
            $('.lp-toggle-container .payment-type.lp-split-payment').addClass('active');
            $('.limepay-installment-offer__shortcode .limepay-installment-price').addClass('active');
        } else {
            _deleteCookie('lp-preferred-bnpl-option');
            $('.lp-toggle-container .payment-type').removeClass('active');
            $('.limepay-installment-offer__shortcode .limepay-installment-price').removeClass('active')
            $('.lp-toggle-container .payment-type.lp-one-time').addClass('active');
        }
    });
});
