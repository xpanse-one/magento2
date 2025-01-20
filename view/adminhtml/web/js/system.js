require([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'domReady!'
], function ($, alert, $t) {
    window.xpanseValidator = function (endpoint, environmentId, skip = false) {
        environmentId = $('[data-ui-id="' + environmentId + '"]').val();

        let publicId = '', privateId = '';

        if (environmentId === 'sandbox') {
            publicId = $('[data-ui-id="password-groups-xpanse-fields-sandbox-public-key-value"]').val();
            privateId = $('[data-ui-id="password-groups-xpanse-fields-sandbox-secret-key-value"]').val();
        } else {
            publicId = $('[data-ui-id="password-groups-xpanse-fields-public-key-value"]').val();
            privateId = $('[data-ui-id="password-groups-xpanse-fields-secret-key-value"]').val();
        }

        /* Remove previous success message if present */
        if ($(".xpanse-credentials-success-message")) {
            $(".xpanse-credentials-success-message").remove();
        }

        /* Basic field validation */
        let errors = [];

        if (!publicId) {
            errors.push($t('Please enter a Public Key'));
        }

        if (!privateId) {
            errors.push($t('Please enter a Private Key'));
        }

        if (errors.length > 0) {
            alert({
                title: $t('Xayfurl Credential Validation Failed'),
                content:  errors.join('<br />')
            });
            return false;
        }

        $(this).text($t("We're validating your credentials...")).attr('disabled', true);

        const self = this;
        $.ajax({
            type: 'POST',
            url: endpoint,
            data: {
                environment: environmentId,
                public_key: publicId,
                private_key: privateId
            },
            showLoader: true,
            success: function (result) {
                if (result.success === 'true') {
                    if (skip === true) {
                        $('<div class="message message-success xpanse-credentials-success-message">' + $t("Your credentials are valid.") + '</div>').insertAfter($('.paypal-styling-buttons'));
                    } else {
                        $('<div class="message message-success xpanse-credentials-success-message">' + $t("Your credentials are valid.") + '</div>').insertAfter(self);
                    }
                } else {
                    alert({
                        title: $t('Xayfurl Credential Validation Failed'),
                        content: $t('Your Xayfurl Credentials could not be validated. Please ensure you have selected the correct environment and entered a valid Public Key and Private Key.')
                    });
                }
            }
        }).always(function () {
            $(self).text($t("Validate Credentials")).attr('disabled', false);
        });
    };
});
