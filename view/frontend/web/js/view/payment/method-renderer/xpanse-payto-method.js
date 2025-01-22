define(
  [
    'ko',
    'jquery',
    'xpanse_Payment/js/view/payment/method-renderer/xpanse-base-method',
    'Magento_Checkout/js/model/quote',
    'xpanse_Payment/js/model/xpanse',
    'xpanse_Payment/js/model/xpanse-configuration',
  ],
  function (ko, $, xpanseBaseMethod, quote, xpanse, xpanseConfig) {
    'use strict';

    return xpanseBaseMethod.extend({
      defaults: {
        template: 'xpanse_Payment/payment/xpanse-payto-form',
      },
      getCode: function() {
        return 'xpanse_payto';
      },
      getTitle: function() {
        return 'PayTo';
      },
      getFormId: function () {
        return 'xpanse-payto-form';
      },
      initPaymentPayToForm: function () {
        xpanse
          .addPayTo(
            this.getFormId(),
            this.getTotal(),
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

