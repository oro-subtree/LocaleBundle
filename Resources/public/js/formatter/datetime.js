/*global define*/
define(['../locale-settings', 'moment', 'orotranslation/js/translator'
    ], function (localeSettings, moment, __) {
    'use strict';

    var datetimeVendor = 'moment';

    /**
     * Datetime formatter
     *
     * @export  orolocale/js/formatter/datetime
     * @name    orolocale.formatter.datetime
     */
    return {
        /**
         * @property {Object}
         */
        frontendFormats: {
            'day':      localeSettings.getVendorDateTimeFormat(datetimeVendor, 'day'),
            'date':     localeSettings.getVendorDateTimeFormat(datetimeVendor, 'date'),
            'time':     localeSettings.getVendorDateTimeFormat(datetimeVendor, 'time'),
            'datetime': localeSettings.getVendorDateTimeFormat(datetimeVendor, 'datetime')
        },

        /**
         * @property {Object}
         */
        backendFormats: {
            'date':     'YYYY-MM-DD',
            'time':     'HH:mm:ss',
            'datetime': 'YYYY-MM-DD[T]HH:mm:ssZZ',
            'datetime_separator': 'T'
        },

        /**
         * @property {string}
         */
        timezoneOffset: localeSettings.getTimeZoneOffset(),

        /**
         * @returns {string}
         */
        getDayFormat: function () {
            return this.frontendFormats.day;
        },

        /**
         * @returns {string}
         */
        getDateFormat: function () {
            return this.frontendFormats.date;
        },

        /**
         * @returns {string}
         */
        getTimeFormat: function () {
            return this.frontendFormats.time;
        },

        /**
         * @returns {string}
         */
        getDateTimeFormat: function () {
            return this.frontendFormats.datetime;
        },

        /**
         * Return separator between date and time for current format
         *
         * @returns {string}
         */
        getDateTimeFormatSeparator: function() {
            return localeSettings.getDateTimeFormatSeparator();
        },

        /**
         * Return separator between date and time for backend format
         *
         * @returns {string}
         */
        getBackendDateTimeFormatSeparator: function() {
            return this.backendFormats.datetime_separator;
        },

        /**
         * Matches any date value to custom format
         *
         * @param {string} value
         * @param {string|Array.<string>} format
         * @param {boolean=} strict by default its true
         * @returns {boolean}
         */
        isValueValid: function (value, format, strict) {
            return moment(value, format, strict !== false).isValid();
        },

        /**
         * Checks if passed date value matches frontend format
         *
         * @param {string} value
         * @param {boolean=} strict
         * @returns {boolean}
         */
        isDateValid: function (value, strict) {
            return this.isValueValid(value, this.getDateFormat(), strict);
        },

        /**
         * Checks if passed time value matches frontend format
         *
         * @param {string} value
         * @param {boolean=} strict
         * @returns {boolean}
         */
        isTimeValid: function (value, strict) {
            return this.isValueValid(value, this.getTimeFormat(), strict);
        },

        /**
         * Checks if passed date time value matches frontend format
         *
         * @param {string} value
         * @param {boolean=} strict
         * @returns {boolean}
         */
        isDateTimeValid: function (value, strict) {
            return this.isValueValid(value, this.getDateTimeFormat(), strict);
        },

        /**
         * Checks if passed date value matches backend format
         *
         * @param {string} value
         * @param {boolean=} strict
         * @returns {boolean}
         */
        isBackendDateValid: function (value, strict) {
            return this.isValueValid(value, this.backendFormats.date, strict);
        },

        /**
         * Checks if passed time value matches backend format
         *
         * @param {string} value
         * @param {boolean=} strict
         * @returns {boolean}
         */
        isBackendTimeValid: function (value, strict) {
            return this.isValueValid(value, this.backendFormats.time, strict);
        },

        /**
         * Checks if passed date time value matches backend format
         *
         * @param {string} value
         * @param {boolean=} strict
         * @returns {boolean}
         */
        isBackendDateTimeValid: function (value, strict) {
            return this.isValueValid(value, this.backendFormats.datetime, strict);
        },

        /**
         * @param {string} value
         * @returns {string}
         */
        formatDate: function (value) {
            return this.getMomentForBackendDate(value).format(this.getDateFormat());
        },

        /**
         * Get Date object based on formatted backend date string
         *
         * @param {string} value
         * @returns {Date}
         */
        unformatBackendDate: function (value) {
            return this.getMomentForBackendDate(value).toDate();
        },

        /**
         * Get moment object based on formatted backend date string
         *
         * @param {string} value
         * @returns {moment}
         */
        getMomentForBackendDate: function (value) {
            var momentDate = moment.utc(value);
            if (!momentDate.isValid()) {
                throw new Error('Invalid backend date ' + value);
            }
            return momentDate;
        },

        /**
         * @param {string} value
         * @returns {string}
         */
        formatTime: function (value) {
            return this.getMomentForBackendTime(value).format(this.getTimeFormat());
        },

        /**
         * Get Date object based on formatted backend time string
         *
         * @param {string} value
         * @returns {Date}
         */
        unformatBackendTime: function (value) {
            return this.getMomentForBackendTime(value).toDate();
        },

        /**
         * Get moment object based on formatted backend date string
         *
         * @param {string} value
         * @returns {moment}
         */
        getMomentForBackendTime: function (value) {
            var momentTime = moment.utc(value, ['HH:mm:ss', 'HH:mm']);
            if (!momentTime.isValid()) {
                throw new Error('Invalid backend time ' + value);
            }
            return momentTime;
        },

        /**
         * @param {string} value
         * @returns {string}
         */
        formatDateTime: function (value) {
            return this.getMomentForBackendDateTime(value).format(this.getDateTimeFormat());
        },

        /**
         * @param {string} value
         * @returns {string}
         */
        formatSmartDateTime: function (value) {
            var dateMoment = this.getMomentForBackendDateTime(value);
            var dateOnly = this.formatDate(value);
            var todayMoment = moment.utc().zone(this.timezoneOffset);

            if (dateOnly == todayMoment.format(this.getDateFormat())) {
                // same day, only show time
                return dateMoment.format(this.getTimeFormat());
            } else if (dateOnly == todayMoment.subtract(1, 'days').format(this.getDateFormat())) {
                // yesterday
                return __('Yesterday');
            } else if (dateMoment.year() == todayMoment.year()) {
                // same year, return only day and month
                return dateMoment.format(this.getDayFormat());
            }
            // full date with year
            return dateOnly;
        },

        /**
         * Get Date object based on formatted backend date time string
         *
         * @param {string} value
         * @returns {Date}
         */
        unformatBackendDateTime: function (value) {
            return this.getMomentForBackendDateTime(value).toDate();
        },

        /**
         * Get moment object based on formatted backend date time string
         *
         * @param {string} value
         * @returns {moment}
         */
        getMomentForBackendDateTime: function (value) {
            var momentDateTime = moment.utc(value).zone(this.timezoneOffset);
            if (!momentDateTime.isValid()) {
                throw new Error('Invalid backend datetime ' + value);
            }
            return momentDateTime;
        },

        /**
         * @param {string} value
         * @returns {string}
         */
        convertDateToBackendFormat: function (value) {
            return this.getMomentForFrontendDate(value).format(this.backendFormats.date);
        },

        /**
         * @param {string} value
         * @returns {string}
         */
        convertTimeToBackendFormat: function (value) {
            return this.getMomentForFrontendTime(value).format(this.backendFormats.time);
        },

        /**
         * @param {string} value
         * @param {string} [timezoneOffset]
         * @returns {string}
         */
        convertDateTimeToBackendFormat: function (value, timezoneOffset) {
            return this.getMomentForFrontendDateTime(value, timezoneOffset).format(this.backendFormats.datetime);
        },

        /**
         * Get moment object based on formatted frontend date string
         *
         * @param {string} value
         * @returns {moment}
         */
        getMomentForFrontendDate: function (value) {
            if (this.isDateObject(value)) {
                return this.formatDate(value);
            } else if (!this.isDateValid(value)) {
                throw new Error('Invalid frontend date ' + value);
            }

            return moment.utc(value, this.getDateFormat());
        },

        /**
         * Get Date object based on formatted frontend date string
         *
         * @param {string} value
         * @returns {Date}
         */
        unformatDate: function (value) {
            return this.getMomentForFrontendDate(value).toDate();
        },

        /**
         * Get moment object based on formatted frontend time string
         *
         * @param {string} value
         * @returns {moment}
         */
        getMomentForFrontendTime: function (value) {
            if (this.isDateObject(value)) {
                value = this.formatTime(value);
            } else if (!this.isTimeValid(value)) {
                throw new Error('Invalid frontend time ' + value);
            }

            return moment.utc(value, this.getTimeFormat());
        },

        /**
         * Get Date object based on formatted frontend time string
         *
         * @param {string} value
         * @returns {Date}
         */
        unformatTime: function (value) {
            return this.getMomentForFrontendTime(value).toDate();
        },

        /**
         * Get moment object based on formatted frontend date time string
         *
         * @param {string} value
         * @param {string} [timezoneOffset]
         * @returns {moment}
         */
        getMomentForFrontendDateTime: function (value, timezoneOffset) {
            if (this.isDateObject(value)) {
                value = this.formatDateTime(value);
            } else if (!this.isDateTimeValid(value)) {
                throw new Error('Invalid frontend datetime ' + value);
            }

            timezoneOffset = timezoneOffset || this.timezoneOffset;

            var datetimeFormat = this.getDateTimeFormat();
            // tell which timezone must be used
            if (datetimeFormat.indexOf('Z') === -1) {
                datetimeFormat += ' Z';
                value += ' ' + timezoneOffset;
            }

            return moment.utc(value, datetimeFormat).zone(timezoneOffset);
        },

        /**
         * Get Date object based on formatted frontend date time string
         *
         * @param {string} value
         * @param {string} [timezoneOffset]
         * @returns {Date}
         */
        unformatDateTime: function (value, timezoneOffset) {
            return this.getMomentForFrontendDateTime(value, timezoneOffset).toDate();
        },

        /**
         * Check that obj is Date object
         *
         * @private
         * @param {string|Date} obj
         * @returns {boolean}
         */
        isDateObject: function (obj) {
            return Object.prototype.toString.call(obj) == '[object Date]'
        }
    }
});
