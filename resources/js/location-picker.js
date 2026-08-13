import { Country, State, City } from 'country-state-city';

export const GeoLocation = {
    Country,
    State,
    City,
};

export function locationField(initialData, config = {}) {
    let raw = initialData;
    if (typeof raw === 'string' && raw.trim().startsWith('{')) {
        try {
            raw = JSON.parse(raw);
        } catch (e) {
            raw = raw.trim() ? { formatted: raw } : {};
        }
    } else if (typeof raw === 'string') {
        raw = raw.trim() ? { formatted: raw } : {};
    } else if (!raw || typeof raw !== 'object') {
        raw = {};
    }

    const enableCountry = config.enable_country !== undefined ? !!config.enable_country : (config.enableCountry !== undefined ? !!config.enableCountry : true);
    const enableState = config.enable_state !== undefined ? !!config.enable_state : (config.enableState !== undefined ? !!config.enableState : true);
    const enableCity = config.enable_city !== undefined ? !!config.enable_city : (config.enableCity !== undefined ? !!config.enableCity : true);

    let initialCountry = raw.country || '';
    let initialCountryCode = raw.country_code || raw.countryCode || '';
    let initialCountryFlag = raw.country_flag || raw.countryFlag || '';
    let initialState = raw.state || '';
    let initialStateCode = raw.state_code || raw.stateCode || '';
    let initialCity = raw.city || '';
    let initialFormatted = raw.formatted || '';

    // If countryCode is missing but country name is present, find matching country
    if (!initialCountryCode && initialCountry) {
        const all = Country.getAllCountries();
        const found = all.find(c => c.name.toLowerCase() === initialCountry.toLowerCase() || c.isoCode.toLowerCase() === initialCountry.toLowerCase());
        if (found) {
            initialCountryCode = found.isoCode;
            initialCountry = found.name;
            initialCountryFlag = found.flag;
        }
    } else if (initialCountryCode && !initialCountryFlag) {
        const found = Country.getCountryByCode(initialCountryCode);
        if (found) {
            initialCountryFlag = found.flag;
            if (!initialCountry) initialCountry = found.name;
        }
    }

    // If state is present but stateCode is missing, find matching state
    if (initialCountryCode && initialState && !initialStateCode) {
        const states = State.getStatesOfCountry(initialCountryCode);
        const found = states.find(s => s.name.toLowerCase() === initialState.toLowerCase() || s.isoCode.toLowerCase() === initialState.toLowerCase());
        if (found) {
            initialStateCode = found.isoCode;
            initialState = found.name;
        }
    }

    return {
        enableCountry,
        enableState,
        enableCity,

        country: initialCountry,
        countryCode: initialCountryCode,
        countryFlag: initialCountryFlag,
        state: initialState,
        stateCode: initialStateCode,
        city: initialCity,
        formatted: initialFormatted || [initialCity, initialState, initialCountry].filter(Boolean).join(', '),

        countryOpen: false,
        countrySearch: '',

        stateOpen: false,
        stateSearch: '',

        cityOpen: false,
        citySearch: '',

        get countries() {
            const all = Country.getAllCountries();
            if (!this.countrySearch.trim()) {
                return all;
            }
            const q = this.countrySearch.toLowerCase();
            return all.filter(c => c.name.toLowerCase().includes(q) || c.isoCode.toLowerCase().includes(q));
        },

        get states() {
            if (this.enableCountry && !this.countryCode) return [];
            let all = [];
            if (this.countryCode) {
                all = State.getStatesOfCountry(this.countryCode);
            } else {
                all = State.getAllStates();
            }
            if (!this.stateSearch.trim()) {
                return all.slice(0, 100);
            }
            const q = this.stateSearch.toLowerCase();
            return all.filter(s => s.name.toLowerCase().includes(q) || s.isoCode.toLowerCase().includes(q)).slice(0, 100);
        },

        get cities() {
            if (this.enableCountry && !this.countryCode) return [];
            let all = [];
            if (this.countryCode && this.stateCode) {
                all = City.getCitiesOfState(this.countryCode, this.stateCode);
            } else if (this.countryCode) {
                all = City.getCitiesOfCountry(this.countryCode) || [];
            } else {
                all = City.getAllCities() || [];
            }
            if (!this.citySearch.trim()) {
                return all.slice(0, 150);
            }
            const q = this.citySearch.toLowerCase();
            return all.filter(c => c.name.toLowerCase().includes(q)).slice(0, 150);
        },

        selectCountry(c) {
            if (!c) {
                this.country = '';
                this.countryCode = '';
                this.countryFlag = '';
            } else {
                this.country = c.name;
                this.countryCode = c.isoCode;
                this.countryFlag = c.flag;
            }
            this.state = '';
            this.stateCode = '';
            this.city = '';
            this.countryOpen = false;
            this.countrySearch = '';
            this.updateFormatted();
        },

        selectState(s) {
            if (!s) {
                this.state = '';
                this.stateCode = '';
            } else {
                this.state = s.name;
                this.stateCode = s.isoCode;
            }
            this.city = '';
            this.stateOpen = false;
            this.stateSearch = '';
            this.updateFormatted();
        },

        selectCity(name) {
            this.city = name || '';
            this.cityOpen = false;
            this.citySearch = '';
            this.updateFormatted();
        },

        clearAll() {
            this.country = '';
            this.countryCode = '';
            this.countryFlag = '';
            this.state = '';
            this.stateCode = '';
            this.city = '';
            this.formatted = '';
            this.countrySearch = '';
            this.stateSearch = '';
            this.citySearch = '';
        },

        updateFormatted() {
            const parts = [];
            if (this.enableCity && this.city) parts.push(this.city);
            if (this.enableState && this.state) parts.push(this.state);
            if (this.enableCountry && this.country) parts.push(this.country);
            this.formatted = parts.join(', ');
        }
    };
}

if (typeof window !== 'undefined') {
    window.GeoLocation = GeoLocation;
    window.locationField = locationField;
}
