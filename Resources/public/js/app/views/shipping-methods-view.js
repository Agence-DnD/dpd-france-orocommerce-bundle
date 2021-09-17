define(function(require) {
    'use strict';

    const BaseView = require('oroui/js/app/views/base/view');
    const _ = require('underscore');
    const $ = require('jquery');
    const NumberFormatter = require('orolocale/js/formatter/number');
    const mediator = require('oroui/js/mediator');
    const templatePredict = require('tpl-loader!dnddpdfranceshipping/templates/view/method-predict-details.html')
    const templatePickup = require('tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-details.html')


    const ShippingMethodsView = BaseView.extend({
        autoRender: true,

        options: {
            template: ''
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
                methodDetails: this.methodDetails
            }));
            this.$el.html($el);
        },

        methodDetails: function(identifier) {
            if (identifier === "dpd_fr_predict") {
                return templatePredict();
            } else if (identifier === "dpd_fr_pickup") {
                return templatePickup();
            }
        }
    });

    return ShippingMethodsView;
});
