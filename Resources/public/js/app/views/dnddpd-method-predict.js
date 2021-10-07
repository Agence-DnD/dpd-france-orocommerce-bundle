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

        this.render();
        this.loadingMaskView = new LoadingMaskView({container: this.$el});
        this.$deliveryPhone = $(this.options.phoneSelector);
        this.$hiddenDeliveryPhone = $(this.options.hiddenInputs.deliveryPhone);
        this.$error = $(this.options.errorSelector);
    },

    /**
     * Render method details
     */
    render: function() {
        const phone = $(this.options.filledInputs.addressPhone).val();

        const $el = $(this.template({
            phone: phone
        }));

        this.$el.html($el);
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
        }
    },

    /**
     *
     * Set delivery phone value
     * @param number
     * @private
     */
    _setDeliveryPhone: function(number) {
        this.$hiddenDeliveryPhone.val(number);
    }
});

export default DndDpdMethodPredict;
