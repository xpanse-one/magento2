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

    window._quote = quote;
    return xayfurlBaseMethod.extend({
      self: this,
      defaults: {
        template: 'xpanse_Payment/payment/xpanse-checkout-form',
      },
      getCode: function() {
        return `xpanse_checkout_${this.getProviderType()}`;
      },
      getTitle: function() {
        const info = xpanseConfig.getCheckoutInfo(this.getProviderType());
        return info?.name || 'Checkout';
      },
      getProviderType: function() {
        return this.provider?.providerType;
      },
      getProviderLogo: function() {
        if (this.getProviderType() === "azupay") return "payid";
        return this.getProviderType();
      },
      getFormId: function() {
        return `xpanse-checkout-form-${this.getProviderType()}`;
      },
      initPaymentCheckoutForm: function () {
        xpanse
          .addCheckout(
            this.getFormId(),
            this.getTotal(),
            this.getCurrency(),
            this.getProviderType(),
            this.provider?.providerId,
            {},
            this.provider?.options
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

