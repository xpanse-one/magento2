define(
  [
    'ko',
    'jquery',
    'xpanse_Payment/js/view/payment/method-renderer/xpanse-base-method',
    'Magento_Checkout/js/model/quote',
    'xpanse_Payment/js/model/xpanse',
    'xpanse_Payment/js/model/xpanse-configuration',
  ],
  function (ko, $, XayfurlBaseMethod, quote, xpanse, xpanseConfig) {
    'use strict';

    return XayfurlBaseMethod.extend({
      defaults: {
        template: 'xpanse_Payment/payment/xpanse-paypal-form',
      },
      getCode: function() {
        return 'xpanse_paypal';
      },
      getTitle: function() {
        return 'Paypal';
      },
      getFormId: function () {
        return 'xpanse-paypal-form';
      },
      initPaymentPaypalForm: function () {
        xpanse
          .addPaypal(
            this.getFormId(),
            this.getTotal(),
            this.getCurrency(),
          );

        return this;
      },
      getData: function () {
        return {
          'method': this.item.method,
          'additional_data': {
            'xpanseToken': xpanseConfig.getToken(),
            'xpanseChargeId': xpanseConfig.getChargeId(),
            'paymentMethodId': xpanseConfig.getPaymentMethodId(),
            'xpanseSaveMyPayment': xpanseConfig.getSaveMyPaymentMethod(),
          },
        };
      },
    });
  },
);

