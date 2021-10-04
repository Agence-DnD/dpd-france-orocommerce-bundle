import BaseView from 'oroui/js/app/views/base/view';
import _ from 'underscore';
import $ from 'jquery';
import NumberFormatter from 'orolocale/js/formatter/number';
import mediator from 'oroui/js/mediator';
import DndDpdMethodPickup from 'dnddpdfranceshipping/js/app/views/dnddpd-method-pickup';
import DndDpdMethodPredict from 'dnddpdfranceshipping/js/app/views/dnddpd-method-predict';


const ShippingMethodsView = BaseView.extend({
    autoRender: true,

    options: {
        template: '',

        methodContainerSelector: '.dnddpd-method',

        filledInputs: {
            zipCode: '#dpd_fr_shipping_address_zipcode',
            addressCity: '#dpd_fr_shipping_address_city',
            addressStreet: '#dpd_fr_shipping_address_street',
            addressPhone: '#dpd_fr_shipping_address_phone',
            googleMapsApi: '#dpd_fr_google_maps_api_key'
        },

        hiddenInputs: {
            deliveryPhone: '[name*="delivery_phone"]',
            relayId: '[name*="dpd_fr_relay_id"]'
        },

        predictId: 'dpd_fr_predict',

        pickupId: 'dpd_fr_pickup'
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

    /**
     * Render shipping methods
     *
     * @param options
     */
    render: function(options) {
        this.updateShippingMethods(options);
        mediator.trigger('layout:adjustHeight');
        mediator.trigger('checkout:shipping-method:rendered');
    },

    /**
     * override to add method details
     *
     * @param options
     * @overrides
     */
    updateShippingMethods: function(options) {
        const data = this.options.data;
        const $el = $(this.options.template({
            methods: options || data.methods,
            currentShippingMethod: data.currentShippingMethod,
            currentShippingMethodType: data.currentShippingMethodType,
            formatter: NumberFormatter
        }));
        this.$el.html($el);
        this._methodDetails();
    },

    /**
     * Render methods predict and pickup details
     */
    _methodDetails: function() {
        this.subview('checkoutShippingMethodPickup', new DndDpdMethodPickup({
            el: this.$el.find(`[data-method-detail="${this.options.pickupId}"]`),
            filledInputs: this.options.filledInputs,
            hiddenInputs: this.options.hiddenInputs
        }));

        this.subview('checkoutShippingMethodPredict', new DndDpdMethodPredict({
            el: this.$el.find(`[data-method-detail="${this.options.predictId}"]`),
            filledInputs: this.options.filledInputs,
            hiddenInputs: this.options.hiddenInputs
        }));
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
    },

    /**
     * Set delivery phone value
     *
     * @param number
     * @private
     */
    _setDeliveryPhone: function(number) {
        const $input = $(this.options.hiddenInputs.deliveryPhone);
        $input.val(number);
    }
});

export default ShippingMethodsView;
