define(
  [
    'xpansejs',
    'Xpanse_Payment/js/model/xpanse-configuration'
  ],
  function(pf, xpanseConfig) {
    'use strict';

    return pf
      .init(xpanseConfig.getEnv(), xpanseConfig.getPublicKey(), xpanseConfig.isDebug())
      .preloadClickToPay();
  },
);
