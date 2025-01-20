define(
  [
    'ko',
    'jquery',
    'Xpanse_Payment/js/view/payment/method-renderer/xpanse-base-method',
    'Magento_Checkout/js/model/quote',
    'Xpanse_Payment/js/model/xpanse',
    'Xpanse_Payment/js/model/xpanse-configuration',
  ],
  function (ko, $, XayfurlBaseMethod, quote, xpanse, xpanseConfig) {
    'use strict';

    return XayfurlBaseMethod.extend({
      defaults: {
        template: 'Xpanse_Payment/payment/xpanse-applepay-form',
      },
      getCode: function() {
        return 'xpanse_applepay';
      },
      getTitle: function() {
        return 'Apple Pay';
      },
      getFormId: function () {
        return 'xpanse-applepay-form';
      },
      initPaymentApplePayForm: function () {
        xpanse
          .addApplePay(
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

