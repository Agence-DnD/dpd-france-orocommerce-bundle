import BaseView from 'oroui/js/app/views/base/view';
import _ from 'underscore';
import tools from 'oroui/js/tools';
import pickupList from 'tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-list.html';
import pickupDetails from 'tpl-loader!dnddpdfranceshipping/templates/view/method-pickup-modal-content.html';
// import BaseComponent from 'oroui/js/app/components/base/component';
import Modal from 'oroui/js/modal';

const dnddpdMethodPickup = BaseView.extend({
    options: {
        pickupListSelector: '[data-pickup-list]',

        moreDetailsSelector: '[data-pickup-more]',

        form: {
            id: '#pickup-search',
            toggle: '#pickup-search-toggle',
            submitBtn: '#pickup-search-btn',
            addressSelector: '#pickup-address',
            zipCodeSelector: '#pickup-zipcode',
            citySelector: '#pickup-city',
        },

        hiddenRelayIdSelector: '[name*="dpd_fr_relay_id"]'
    },

    pickupAPI: '/dpd_france/relays',

    request: {
        checkoutId: ''
    },

    modal: null,

    // @todo translations
    pickupDays: [
        "Lundi",
        "Mardi",
        "Mercredi",
        "Jeudi",
        "Vendredi",
        "Samedi",
        "Dimanche",
    ],

    map: {
        apiKey: 'AIzaSyCsbUgTPMEbBPUcRi12pBWaSTTsI77_p3w', // @todo get APIkey from hidden field

        markers: [],

        selector: 'pickup-modal-map',

        options: {
            zoom: 15
        },
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

        this.$searchForm = $(this.options.form.id);
        this.$searchSubmitBtn = $(this.options.form.submitBtn);
        this.$toggleSearch = $(this.options.form.toggle);
        this.$pickupList = $(this.options.pickupListSelector);
        this.$hiddenRelayId = $(this.options.hiddenRelayIdSelector);

        // Search form
        this._searchForm();

        this._initForm();
        this._updateSearchInfos();
        this._formatUrl();
        this._initModal();

        $(this.options.form.toggle).on('click', function (event) {
            event.preventDefault()
            this._toggleSearch();
        }.bind(this));
    },

    _setPickupId: function(id) {
        this.$hiddenRelayId.val(id);
        this.$hiddenRelayId.trigger('change');
    },

    _initModal: function(content) {
        this.modal = new Modal({
            className: 'modal oro-modal-normal dnddpd-pickup-details-modal',
            title: false,
            allowCancel: false,
            allowOk: false,
            content: content
        });
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

        this.$searchSubmitBtn.trigger('click');
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
    },

    /**
     * Search form handler
     *
     * @private
     */
    _searchForm: function () {
        this.$searchForm.on('submit', function (event) {
            event.preventDefault()

            this._formatUrl(); // @todo to remove
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
        console.log(e); // @todo reset search form
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

    _toggleSearch: function(){
        this.$searchForm.toggle();
    },

    _noResults: function() {
        // @todo no results message
        this.$pickupList.html('no results');
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

        this._toggleSearch();
    },

    _openDetailsModal: function(e) {
        const pudoId = e.target.getAttribute('data-pickup-more'),
            currentPickup = this.results.filter(pickup => pickup.PUDO_ID === pudoId)[0],
            hours = _.groupBy(currentPickup.OPENING_HOURS_ITEMS.OPENING_HOURS_ITEM, item => item.DAY_ID),
            pickupDetailsContent = pickupDetails({
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

export default dnddpdMethodPickup;
