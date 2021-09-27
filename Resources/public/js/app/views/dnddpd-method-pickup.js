import BaseView from 'oroui/js/app/views/base/view';
import _ from 'underscore';
import tools from 'oroui/js/tools';
import pickupList from 'tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-list.html';
// import BaseComponent from 'oroui/js/app/components/base/component';

const dnddpdMethodPickup = BaseView.extend({
    options: {
        pickupListSelector: '[data-pickup-list]',
        moreDetailsSelector: '[data-pickup-more]',
        form: {
            id: '#pickup-search',
            addressSelector: '#pickup-address',
            zipCodeSelector: '#pickup-zipcode',
            citySelector: '#pickup-city',
        }
    },

    request: {
        checkoutId: ''
    },

    events: {
        'click #pickup-search-reset': '_resetForm'
    },

    constructor: function dnddpdMethodPickup(options) {
        dnddpdMethodPickup.__super__.constructor.call(this, options);
    },

    /**
     *
     * @param options
     */
    initialize: function(options) {
        this.options = _.defaults(options || {}, this.options);
        dnddpdMethodPickup.__super__.initialize.call(this, options);
        console.log('My custom view is initialized',this);

        this.searchForm = $(this.options.form.id);

        this._initForm();
        this._updateSearchInfos();
        this._formatUrl();
        this.$pickupList = $(this.options.pickupListSelector);

        // Search form
        this._searchFormHandler();
    },

    /**
     * Format url
     *
     * @private
     */
    _formatUrl: function() {
        const url = window.location,
              origin = url.origin,
              checkoutId = url.pathname.substring(url.pathname.lastIndexOf('/') + 1);

        this.url = origin;
        this.pickupAPI = '/dpd_france/relays';
        this.request.checkoutId = checkoutId;
    },

    /**
     * Update form
     *
     * @private
     */
    _initForm: function() {
        const addressValue = $(this.options.address).val(),
              zipCode = $(this.options.zipCode).val(),
              city = $(this.options.city).val();

        addressValue && $(this.options.form.addressSelector).val(addressValue);
        zipCode && $(this.options.form.zipCodeSelector).val(zipCode);
        city && $(this.options.form.citySelector).val(city);
    },

    /**
     * Update requested address
     *
     * @private
     */
    _updateSearchInfos: function() {
        _.extend(this.request, {
            address: $(this.options.form.addressSelector).val(),
            postalCode: $(this.options.form.zipCodeSelector).val(),
            city: $(this.options.form.citySelector).val()
        });

        console.log("request", this.request);
    },

    /**
     * Search form handler
     *
     * @private
     */
    _searchFormHandler: function () {
        this.searchForm.on('submit', function (event) {
            event.preventDefault()

            this._formatUrl();
            this._updateSearchInfos();
            this._getPickups();
        }.bind(this));
    },

    /**
     * Reset form
     *
     * @param e
     * @private
     */
    _resetForm: function(e) {
        console.log(e);
    },

    /**
     * Get pickups
     *
     * @private
     */
    _getPickups: function() {
        const url = `${this.url}${this.pickupAPI}?${tools.packToQueryString(this.request)}`;
        console.log(url);

        $.ajax({
            url: url,
            type: 'GET',
            success: _.bind(function(response) {
                this._renderPickupList(response);
            }, this),
            error: function() {
                console.log("error");
            }
        });
    },

    /**
     * Render Pickups
     *
     * @param response
     * @private
     */
    _renderPickupList: function(response) {
        this.results = response.relays.PUDO_ITEMS.PUDO_ITEM;

        const $pickupList = $(pickupList({
            pickups: this.results,
            formatDistance: this._formatDistance
        }));
        this.$pickupList.html($pickupList);
        this.$pickupList.find(this.options.moreDetailsSelector).on('click', this._openDetailsModal);
    },

    _openDetailsModal: function() {
        console.log('open');
    },

    /**
     * Convert distance to km
     *
     * @param distance
     * @return {string}
     * @private
     */
    _formatDistance: function(distance) {
        return (distance / 1000).toFixed(2) + " km";
    }

});

export default dnddpdMethodPickup;
