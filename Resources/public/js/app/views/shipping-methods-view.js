define(function(require) {
    'use strict';

    const BaseView = require('oroui/js/app/views/base/view');
    const _ = require('underscore');
    const $ = require('jquery');
    const NumberFormatter = require('orolocale/js/formatter/number');
    const mediator = require('oroui/js/mediator');
    const predict = require('tpl-loader!dnddpdfranceshipping/templates/view/method-predict-details.html')
    const pickup = require('tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-details.html')


    const ShippingMethodsView = BaseView.extend({
        autoRender: true,

        options: {
            template: '',
            methodContainerSelector: '.dnddpd-method',
            predictSelector: '[data-predict]',
            pickupSelector: '[data-pickup]',
            predict: {
                phoneSelector: '[name="predict_phone"]',
                errorSelector: '[data-error]'
            }
        },

        events: {
            'click [name$="shippingMethodType"]': '_onMethodClick',
        },

        /**
         * @inheritdoc
         */
        constructor: function ShippingMethodsView(options) {
            ShippingMethodsView.__super__.constructor.call(this, options);
        },

        /**
         * @inheritdoc
         */
        initialize: function(options) {
            ShippingMethodsView.__super__.initialize.call(this, options);

            this.options = _.defaults(options || {}, this.options);
            this.options.template = _.template(this.options.template);

            mediator.on('transition:failed', this.render.bind(this, []));
        },

        render: function(options) {
            this.updateShippingMethods(options);
            mediator.trigger('layout:adjustHeight');
            mediator.trigger('checkout:shipping-method:rendered');
        },

        updateShippingMethods: function(options) {
            const data = this.options.data;
            const $el = $(this.options.template({
                methods: options || data.methods,
                currentShippingMethod: data.currentShippingMethod,
                currentShippingMethodType: data.currentShippingMethodType,
                formatter: NumberFormatter,
                methodDetails: this._methodDetails
            }));
            this.$el.html($el);
            this.$predict = this.$el.find(this.options.predictSelector);
            this.$pickup = this.$el.find(this.options.pickupSelector);
            this._validatePhone();
        },

        /**
         * Render predict and pickup details
         *
         * @param identifier
         * @private
         */
        _methodDetails: function(identifier) {
            if (identifier === "dpd_fr_predict") {
                return predict();
            } else if (identifier === "dpd_fr_pickup") {
                return pickup();
            }
        },

        /**
         * Validate predict method phone number
         *
         * @private
         */
        _validatePhone: function() {
            const self = this;
            const error = this.$predict.find(this.options.predict.errorSelector);

            self.$predict.find(self.options.predict.phoneSelector).on('keyup', (e) => {
                const input = e.target,
                      value = input.value,
                      regex = /(0|\+33|0033)[6-7][0-9]{8}/g;

                if (value.match(regex) && value.length === 10) {
                    error.hide();
                    input.removeClass('error');
                } else {
                    error.show();
                    input.addClass('error');
                }
            })
        },

        /**
         * add Class to selected method
         *
         * @param e event
         * @private
         */
        _onMethodClick: function(e) {
            const input = e.target;

            input.checked && $(input).parents(this.options.methodContainerSelector)
                                   .addClass('active')
                                   .siblings()
                                   .removeClass('active');
        }
    });

    return ShippingMethodsView;
});
