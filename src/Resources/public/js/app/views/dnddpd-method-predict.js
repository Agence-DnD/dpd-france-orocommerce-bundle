import BaseView from 'oroui/js/app/views/base/view';
import _ from 'underscore';
import $ from 'jquery';
import LoadingMaskView from 'oroui/js/app/views/loading-mask-view';
import predictDetails from 'tpl-loader!dnddpdfranceshipping/templates/view/method-predict-details.html';

const DndDpdMethodPredict = BaseView.extend({
    options: {
        phoneSelector: '[name="predict_phone"]',
        errorSelector: '[data-error]'
    },

    template: predictDetails,

    events: {
        'keyup [name="predict_phone"]': '_onUpdatePhone',
    },

    /**
     * @inheritdoc
     */
    constructor: function DndDpdMethodPredict(options) {
        DndDpdMethodPredict.__super__.constructor.call(this, options);
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.options = _.defaults(options || {}, this.options);
        DndDpdMethodPredict.__super__.initialize.call(this, options);

        this.$hiddenDeliveryPhone = $(this.options.hiddenInputs.deliveryPhone);

        this.render();
        this.loadingMaskView = new LoadingMaskView({container: this.$el});
        this.$deliveryPhone = $(this.options.phoneSelector);
        this.$error = $(this.options.errorSelector);
        this.$checkoutForm = $(this.options.formSelector);
        this.triggerField();
        this.validateForm();
    },

    /**
     * Render method details
     */
    render: function() {
        const savedPhone = this.$hiddenDeliveryPhone.val(),
            phone = (savedPhone && (savedPhone != 0)) ? savedPhone : $(this.options.shippingAddress).data('phone');

        const $el = $(this.template({
            phone: phone
        }));

        this.$el.html($el);

        (this.isCurrentMethod()) ?
            this._setDeliveryPhone(phone) :
            this._setDeliveryPhone(0);
    },

    /**
     * trigger phone
     */
    triggerField: function() {
        this.$deliveryPhone.trigger('keyup');
    },

    /**
     * validate phone
     *
     * @param e
     * @private
     */
    _onUpdatePhone: function(e) {
        e.preventDefault();

        const input = e.target,
              $input = $(input),
              value = input.value,
              regex = /(0|\+33|0033)[6-7][0-9]{8}/g;

        if (value.match(regex) && value.length === 10) {
            this.$error.hide();
            $input.toggleClass('valid').removeClass('not-valid');
            this._setDeliveryPhone(value);
        } else {
            this.$error.show();
            $input.addClass('not-valid').removeClass('valid');
            this._setDeliveryPhone('');
        }

        if (!this.isCurrentMethod()) {
            this._setDeliveryPhone(0);
        }

        this.validateForm();
    },

    /**
     * validate checkout form
     */
    validateForm: function() {
        const submitBtn = this.$checkoutForm.find('[type="submit"]');
        submitBtn.prop("disabled", !this.$checkoutForm.valid());
    },

    /**
     *
     * Set delivery phone value
     * @param number
     * @private
     */
    _setDeliveryPhone: function (number) {
        this.$hiddenDeliveryPhone.val(number);
        this.$hiddenDeliveryPhone.trigger('change');
    },

    /**
     * Current method 
     */
    isCurrentMethod: function () {
        return $('[name="shippingMethodType"]:checked')[0].getAttribute('data-shipping-type') === this.options.predictId;
    },
});

export default DndDpdMethodPredict;
