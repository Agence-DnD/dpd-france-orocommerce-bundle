import BaseView from 'oroui/js/app/views/base/view';
import _ from 'underscore';
import $ from 'jquery';
import NumberFormatter from 'orolocale/js/formatter/number';
import mediator from 'oroui/js/mediator';
import DndDpdMethodPickup from 'dnddpdfranceshipping/js/app/views/dnddpd-method-pickup';
import DndDpdMethodPredict from 'dnddpdfranceshipping/js/app/views/dnddpd-method-predict';
import validate from 'jquery.validate';

const ShippingMethodsView = BaseView.extend({
    autoRender: true,

    options: {
        template: '',

        methodContainerSelector: '.dnddpd-method',

        formSelector: 'form[name="oro_workflow_transition"]',

        shippingAddress: "#dpd_fr_shipping_address",

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
        this.$checkoutForm = $(this.options.formSelector);
        this.validator = this.$checkoutForm.validate();
        this._addValidatorRules();
    },

    /**
     * Render methods predict and pickup details
     */
    _methodDetails: function() {
        this.subview('checkoutShippingMethodPickup', new DndDpdMethodPickup({
            el: this.$el.find(`[data-method-detail="${this.options.pickupId}"]`),
            shippingAddress: this.options.shippingAddress,
            hiddenInputs: this.options.hiddenInputs,
            formSelector: this.options.formSelector
        }));

        this.subview('checkoutShippingMethodPredict', new DndDpdMethodPredict({
            el: this.$el.find(`[data-method-detail="${this.options.predictId}"]`),
            shippingAddress: this.options.shippingAddress,
            hiddenInputs: this.options.hiddenInputs,
            formSelector: this.options.formSelector
        }));
    },

    _updateHiddenFields: function (method) {
        const hiddenInputs = this.options.hiddenInputs,
            $deliveryPhone = $(hiddenInputs.deliveryPhone),
            $relayId = $(hiddenInputs.relayId),
            selectedMethod = method || this.options.selectedMethod;

        (selectedMethod === this.options.pickupId) &&
            $deliveryPhone.val(0) &&
            $relayId.val('') &&
            this.subview('checkoutShippingMethodPickup').setSelectedMethod();

        (selectedMethod === this.options.predictId) &&
            $relayId.val(0) &&
            $deliveryPhone.val('') &&
            this.subview('checkoutShippingMethodPredict').triggerField();

        ((selectedMethod !== this.options.predictId) && (selectedMethod !== this.options.pickupId)) &&
            $relayId.val(0) &&
            $deliveryPhone.val(0);
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

        this._updateHiddenFields($(e.target).data('shipping-type'));

        this.subview('checkoutShippingMethodPredict').validateForm();
        this.subview('checkoutShippingMethodPickup').validateForm();
    },

    /**
     * Add validations rules
     *
     * @private
     */
    _addValidatorRules: function() {
        const self = this;

        Object.assign(this.validator.settings, {
            rules: {
                'oro_workflow_transition[delivery_phone]': {
                    required: function() {
                        return $(`[data-choice="${self.options.predictId}"]`).is(':checked')
                    }
                },
                'oro_workflow_transition[dpd_fr_relay_id]': {
                    required: function() {
                        return $(`[data-choice="${self.options.pickupId}"]`).is(':checked')
                    }
                }
            }
        });
    }
});

export default ShippingMethodsView;
