define(
  [
    'ko',
    'jquery',
    'xpanse_Payment/js/view/payment/method-renderer/xpanse-base-method',
    'Magento_Checkout/js/model/quote',
    'xpanse_Payment/js/model/xpanse',
    'xpanse_Payment/js/model/xpanse-configuration',
  ],
  function (ko, $, xayfurlBaseMethod, quote, xpanse, xpanseConfig) {
    'use strict';

    return xayfurlBaseMethod.extend({
      defaults: {
        template: 'xpanse_Payment/payment/xpanse-card-form',
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

