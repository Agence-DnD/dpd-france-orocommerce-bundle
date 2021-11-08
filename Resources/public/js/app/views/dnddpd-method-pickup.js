import BaseView from 'oroui/js/app/views/base/view';
import _ from 'underscore';
import __ from 'orotranslation/js/translator';
import tools from 'oroui/js/tools';
import pickupList from 'tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-list.html';
import pickupDetails from 'tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-details.html';
import pickupModal from 'tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-modal-content.html';
import LoadingMaskView from 'oroui/js/app/views/loading-mask-view';
import Modal from 'oroui/js/modal';
import $ from 'jquery';

const DndDpdMethodPickup = BaseView.extend({
    options: {
        pickupListSelector: '[data-pickup-list]',

        moreDetailsSelector: '[data-pickup-more]',

        form: {
            id: '#pickup-search',
            searchWrapper: '#pickup-search-wrapper',
            toggle: '#pickup-search-toggle',
            submitBtn: '#pickup-search-btn',
            resetBtn: '#pickup-search-btn',
            addressSelector: '#pickup-address',
            zipCodeSelector: '#pickup-zipcode',
            citySelector: '#pickup-city',
        },

        noResults: __('dnd_dpd_france_shipping.pickup.no_results')
    },

    template: pickupDetails,

    templateModal: pickupModal,

    pickupAPI: '/dpd_france/relays',

    request: {
        checkoutId: ''
    },

    modal: null,

    pickupDays: [
        __('dnd_dpd_france_shipping.pickup.days.day1'),
        __('dnd_dpd_france_shipping.pickup.days.day2'),
        __('dnd_dpd_france_shipping.pickup.days.day3'),
        __('dnd_dpd_france_shipping.pickup.days.day4'),
        __('dnd_dpd_france_shipping.pickup.days.day5'),
        __('dnd_dpd_france_shipping.pickup.days.day6'),
        __('dnd_dpd_france_shipping.pickup.days.day7')
    ],

    map: {
        apiKey: '',

        markers: [],

        selector: 'pickup-modal-map',

        options: {
            zoom: 15
        },
    },

    events: {
        'click #pickup-search-btn': '_onSearchClick',
        'click #reset-search-reset': '_onResetClick',
        'click #pickup-search-toggle': '_onSearchToggleClick',
    },

    /**
     * @inheritdoc
     */
    constructor: function DndDpdMethodPickup(options) {
        DndDpdMethodPickup.__super__.constructor.call(this, options);
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.options = _.defaults(options || {}, this.options);
        DndDpdMethodPickup.__super__.initialize.call(this, options);

        this.render();
        this.loadingMaskView = new LoadingMaskView({container: this.$el});
        this.$searchForm = $(this.options.form.id);
        this.$searchWrapper = $(this.options.form.searchWrapper);
        this.$searchSubmitBtn = $(this.options.form.submitBtn);
        this.$searchResetBtn = $(this.options.form.resetBtn);
        this.$toggleSearch = $(this.options.form.toggle);
        this.$pickupList = $(this.options.pickupListSelector);
        this.$hiddenRelayId = $(this.options.hiddenInputs.relayId);

        this._initForm();
        this._formatUrl();
    },

    /**
     * show loading mask
     */
    showLoadingMask: function() {
        this.loadingMaskView.show();
    },

    /**
     * hide loading mask
     */
    hideLoadingMask: function() {
        this.loadingMaskView.hide();
    },

    /**
     * on search
     *
     * @private
     */
    _onSearchClick: function() {
        this.showLoadingMask();
        this._updateSearchAddress();
        this._formatUrl();
        this._getPickups();
    },

    /**
     * on reset
     *
     * @private
     */
    _onResetClick: function() {
        this.$searchForm.find('.input').val('');
    },

    /**
     * toggle search
     *
     * @private
     */
    _onSearchToggleClick: function() {
        this.$searchForm.toggle();
        this.$searchWrapper.toggleClass('open');
    },

    /**
     * Format url
     *
     * @private
     */
    _formatUrl: function() {
        const url = window.location,
            checkoutId = url.pathname.substring(url.pathname.lastIndexOf('/') + 1);

        this.url = url.origin;
        this.request.checkoutId = checkoutId;
    },

    /**
     * render method details
     */
    render: function() {
        const $el = $(this.template({
            address: this.request.address,
            postalCode: this.request.postalCode,
            city: this.request.city
        }));

        this.$el.html($el);
    },

    /**
     * set pickup id
     *
     * @param id
     * @private
     */
    _setPickupId: function(id) {
        this.$hiddenRelayId.val(id);
        this.$hiddenRelayId.trigger('change');
    },

    /**
     * Init modal
     *
     * @param content
     * @private
     */
    _initModal: function(content) {
        this.modal = new Modal({
            className: 'modal oro-modal-normal dnddpd-pickup-details-modal',
            title: false,
            cancelButtonClass: 'hidden',
            allowCancel: true,
            allowOk: false,
            content: content
        });
    },

    /**
     * Init search form
     *
     * @private
     */
    _initForm: function() {
        const address = $(this.options.filledInputs.addressStreet).val(),
            zipCode = $(this.options.filledInputs.zipCode).val(),
            city = $(this.options.filledInputs.addressCity).val(),
            googleApi = $(this.options.filledInputs.googleMapsApi).val();

        address && $(this.options.form.addressSelector).val(address);
        zipCode && $(this.options.form.zipCodeSelector).val(zipCode);
        city && $(this.options.form.citySelector).val(city);

        this.map.apiKey = googleApi;
        this._onSearchClick();
    },

    /**
     * Update requested address
     *
     * @private
     */
    _updateSearchAddress: function() {
        _.extend(this.request, {
            address: $(this.options.form.addressSelector).val(),
            postalCode: $(this.options.form.zipCodeSelector).val(),
            city: $(this.options.form.citySelector).val()
        });
    },


    /**
     * Get pickups from API
     *
     * @private
     */
    _getPickups: function() {
        const url = `${this.url}${this.pickupAPI}?${tools.packToQueryString(this.request)}`;

        if (this.request.postalCode && this.request.city) {
            $.ajax({
                url: url,
                type: 'GET',
                success: _.bind(function(response) {
                    if (JSON.stringify(response).includes('PUDO_ITEM') && response.relays.PUDO_ITEMS.PUDO_ITEM.length > 1) {
                        this._renderPickupList(response);
                    } else {
                        this.results = [];
                        this._noResults();
                    }
                }, this),
                error: _.bind(function(error) {
                    this.results = [];
                    console.log(error);
                }, this),
            });
        }
    },

    /**
     * render no result text
     *
     * @private
     */
    _noResults: function() {
        this.$pickupList.html(`<p class="no-results">${this.options.noResults}</p>`);
        this.hideLoadingMask();
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

        this.$pickupList.find(this.options.moreDetailsSelector).on('click', _.bind(this._openDetailsModal, this));
        this.$pickupList.find('.custom-radio').on('click', function (event) {
            const id = event.target.closest('[data-pickup-id]').getAttribute('data-pickup-id');

            this._setPickupId(id);
        }.bind(this));

        this._onSearchToggleClick();
        this.hideLoadingMask();
    },

    /**
     * Open modal
     *
     * @param e
     * @private
     */
    _openDetailsModal: function(e) {
        const pudoId = e.target.getAttribute('data-pickup-more'),
              currentPickup = this.results.filter(pickup => pickup.PUDO_ID === pudoId)[0],
              hours = _.groupBy(currentPickup.OPENING_HOURS_ITEMS.OPENING_HOURS_ITEM, item => item.DAY_ID),
              pickupDetailsContent = this.templateModal({
                  name: currentPickup.NAME,
                  address1: currentPickup.ADDRESS1,
                  address2: currentPickup.ADDRESS2,
                  address3: currentPickup.ADDRESS3,
                  zipCode: currentPickup.ZIPCODE,
                  city: currentPickup.CITY,
                  hours: hours,
                  pickupDays: this.pickupDays,
                  distance: this._formatDistance(currentPickup.DISTANCE),
                  identifier: pudoId
              });

        this.map.options.center = {
            lat: parseFloat(currentPickup.LATITUDE.replace(',','.')),
            lng: parseFloat(currentPickup.LONGITUDE.replace(',','.'))
        };

        this._initModal(pickupDetailsContent);

        // Set map
        this._loadGoogleMaps();
        this.modal.open();
    },

    /**
     * Load Google Maps asynchronously
     *
     * @private
     */
    _loadGoogleMaps: function () {
        const API_URL = 'https://maps.googleapis.com/maps/api/',
              API_KEY = this.map.apiKey;

        $.getScript(`${API_URL}js?key=${API_KEY}`)
        .done(function (script, textStatus) {
            this._initMap()
        }.bind(this))
        .fail(function (jqxhr, settings, exception) {
            console.log(jqxhr)
        });
    },

    /**
     * Init Map
     *
     * @private
     */
    _initMap: function() {
        this.googleMap = new google.maps.Map(document.getElementById(this.map.selector), {
            zoom: this.map.options.zoom,
            center: this.map.options.center,
            fullscreenControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER,
            }
        });

        this.map.markupUrl = this.url+document.getElementById(this.map.selector).getAttribute('data-markup-url');

        this.markerMap = new google.maps.Marker({
            position: new google.maps.LatLng(this.map.options.center.lat, this.map.options.center.lng),
            icon: this.map.markupUrl,
            map: this.googleMap
        });
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

export default DndDpdMethodPickup;
