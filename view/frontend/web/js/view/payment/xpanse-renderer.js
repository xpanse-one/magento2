define(
  [
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list',
    'xpanse_Payment/js/model/xpanse-configuration'
  ],
  function (
    Component,
    rendererList,
    xpanseConfig
  ) {
    'use strict';

    window._checkoutConfig = window.checkoutConfig.payment;
    const providersInfo = window.checkoutConfig.payment?.xpanse?.providersInfo;
    if (!providersInfo) {
      return Component.extend({});
    }

    if (providersInfo.hasCardProviders) {
      rendererList.push(
        {
          type: 'xpanse_card',
          component: 'xpanse_Payment/js/view/payment/method-renderer/xpanse-card-method',
        },
      );
    }

    if (providersInfo.hasPaypalProviders) {
      rendererList.push(
        {
          type: 'xpanse_paypal',
          component: 'xpanse_Payment/js/view/payment/method-renderer/xpanse-paypal-method',
        },
      );
    }

    if (xpanseConfig.isGooglePayEnabled() && providersInfo.hasGooglePayProviders) {
      rendererList.push(
        {
          type: 'xpanse_googlepay',
          component: 'xpanse_Payment/js/view/payment/method-renderer/xpanse-googlepay-method',
        },
      );
    }

    if (xpanseConfig.isSafari() && xpanseConfig.isApplePayEnabled() && providersInfo.hasApplePayProviders) {
      rendererList.push(
        {
          type: 'xpanse_applepay',
          component: 'xpanse_Payment/js/view/payment/method-renderer/xpanse-applepay-method',
        },
      );
    }

    if (providersInfo.hasBnplProviders) {
      for (const provider of providersInfo.bnplProviders) {
        let type = provider.providerType;
        rendererList.push(
          {
            type: `xpanse_checkout_${type}`,
            component: 'xpanse_Payment/js/view/payment/method-renderer/xpanse-checkout-method',
            config: {
              provider: provider,
            }
          },
        );
      }
    }

    if (providersInfo.hasPayToProviders) {
      rendererList.push(
        {
          type: 'xpanse_payto',
          component: 'xpanse_Payment/js/view/payment/method-renderer/xpanse-payto-method',
        },
      );
    }

    /** Add view logic here if needed */
    return Component.extend({});
  },
);
