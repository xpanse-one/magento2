define(
  [
    'ko',
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'Xpanse_Payment/js/model/xpanse',
    'Xpanse_Payment/js/model/xpanse-configuration',
    'Magento_Checkout/js/model/payment/additional-validators',
  ],
  function (ko, $, Component, quote, xpanse, xpanseConfig, additionalValidators) {
    'use strict';

    window._quote = quote;
    window._component = Component;

    return Component.extend({
      self: this,
      isAvailable: ko.observable(true),
      placeOrderButtonVisible: true,
      isCustomerLoggedIn: window.checkoutConfig.isCustomerLoggedIn,
      totals: quote.totals,
      checkCard: ko.observable('saved'),
      customerEmail: window.checkoutConfig.customerData?.email || quote.guestEmail,

      defaults: {
        template: 'Xpanse_Payment/payment/xpanse-card-form',
      },
      getCode: function() {
        return 'xpanse_card';
      },
      getTitle: function() {
        return xpanseConfig.getTitle();
      },
      getTotal: function () {
        return this.getSegment('grand_total').value;
      },
      getCurrency: function () {
        return quote.getTotals()()['quote_currency_code'];
      },
      initialize: function () {
        this._super();

        const self = this;

        quote.totals.subscribe(function () {
          self.setOrderInfo(quote.getTotals()(), quote.getItems());
        });
        quote.billingAddress.subscribe(function () {
          self.setBillingAddress(quote.billingAddress());
        });
        this.subscribeForGuestEmail();

        xpanse.onSuccess(function (response) {
          const xpanseToken = response.token || '';
          const xpanseChargeId = response.transactionId || '';

          xpanseConfig.setToken(xpanseToken);
          xpanseConfig.setChargeId(xpanseChargeId);
          if (self.isCustomerLoggedIn) {
            xpanseConfig.setSaveMyPaymentMethod($('#save_my_payment_method').is(':checked'));
          }

          self.isPlaceOrderActionAllowed(true);
          self.placeOrder();
        });
        xpanse.onFailure(function (errorMessage) {
          self.isPlaceOrderActionAllowed(false);
        });
        xpanse.onValidate(additionalValidators.validate.bind(additionalValidators));


        this.setBillingAddress(quote.billingAddress());
        this.setOrderInfo(quote.getTotals()(), quote.getItems());

        return this;
      },
      setBillingAddress: function (billingAddress) {
        let phone = billingAddress?.telephone?.replace(/^0/, '+61') || "";
        if (phone && !phone.match(/^\+/)) {
          phone = '+61' + phone;
        }
        xpanse
          .setBillingAddress({
            firstName: billingAddress?.firstname,
            lastName: billingAddress?.lastname,
            email: this.customerEmail,
            phoneNumber: phone,
            streetAddress: billingAddress?.street?.[0],
            city: billingAddress?.city,
            country: billingAddress?.countryId,
            region: billingAddress?.region,
            postalCode: billingAddress?.postcode,
            state: billingAddress?.regionCode,
          })
          .setCustomerInfo({
            firstName: billingAddress?.firstname,
            lastName: billingAddress?.lastname,
            email: this.customerEmail,
            phoneNumber: phone,
          });

        return this;
      },
      setOrderInfo: function (quoteTotal, items) {
        xpanse
          .setAmount(quoteTotal?.grand_total)
          .setOrderInfo({
            orderItems: items?.map(p => ({
              name: p.name,
              quantity: Number(p.qty),
              sku: p.sku,
              unitPrice: Number(p.price),
              totalAmount: Number(p.price) * Number(p.qty),
            })),
            taxAmount: Number(quoteTotal?.tax_amount),
            shippingAmount: Number(quoteTotal?.shipping_amount),
            discountAmount: Math.abs(Number(quoteTotal?.discount_amount)),
          })

        return this;
      },
      setCustomerEmail: function(email) {
        const billingAddress = quote.billingAddress();
        let phone = billingAddress?.telephone?.replace(/^0/, '+61') || "";
        if (phone && !phone.match(/^\+/)) {
          phone = '+61' + phone;
        }
        xpanse
          .setCustomerInfo({
            firstName: billingAddress?.firstname,
            lastName: billingAddress?.lastname,
            email,
            phoneNumber: phone,
          });
      },
      subscribeForGuestEmail: function () {
        if (window.checkoutConfig.customerData?.email) return;
        let email = quote.guestEmail;
        const subscribeForGuestEmailHandler = setInterval(() => {
          // If customer is logged in, we don't need to subscribe for guest email
          if (window.checkoutConfig.customerData?.email) {
            clearInterval(subscribeForGuestEmailHandler);
          }
          // check if email is changed
          if (email === quote.guestEmail) return;
          email = quote.guestEmail;
          this.setCustomerEmail(quote.guestEmail);
        }, 500);
      },
      getSavedPaymentMethods: function () {
        let savedPayments = xpanseConfig.getSavedPaymentMethods();
        if (!savedPayments || savedPayments.length === 0) {
          this.checkCard('new');
          return;
        }

        return savedPayments;
      },
      placeOrderWithSavedCard: function () {
        this.isPlaceOrderActionAllowed(false);
        xpanseConfig.setPaymentMethodId($('#xpanse-checkout-saved-select').val());
        this.isPlaceOrderActionAllowed(true);
        this.placeOrder();

        return this;
      },
      /**
       * @param {*} code
       * @return {*}
       */
      getSegment: function (code) {
        let i, total;

        if (!this.totals()) {
          return null;
        }

        for (i in this.totals()['total_segments']) {
          total = this.totals()['total_segments'][i];

          if (String(total.code) === String(code)) {
            return total;
          }
        }

        return null;
      },
    });
  },
);

