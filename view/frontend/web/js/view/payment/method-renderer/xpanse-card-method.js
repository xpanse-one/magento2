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
        template: 'Xpanse_Payment/payment/xpanse-card-form',
      },
      getCode: function () {
        return 'xpanse_card';
      },
      getTitle: function () {
        return xpanseConfig.getTitle();
      },
      getFormId: function () {
        return 'xpanse-card-form';
      },
      initPaymentCardForm: function () {
        xpanse
          .addCardLeastCost(
            this.getFormId(),
            this.getTotal(),
            this.getCurrency(),
            {
              threeDSEmail: this.customerEmail,
              style: "compact"
            },
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

