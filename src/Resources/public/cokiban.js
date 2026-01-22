document.addEventListener('alpine:init', () => {
    // eslint-disable-next-line no-undef
    Alpine.prefix('data-x-');

    // eslint-disable-next-line no-undef
    Alpine.store('cokiban', {
        id: null,
        name: '',
        version: null,
        days: null,
        active: true,
        date: null,
        showBanner: false,
        showHint: false,
        hint: { text: '...', title: 'Accept', button: 'Accept', function: null },
        details: false,
        labels: {},
        cache: {},
        valid: {},
        googleConsentMode: false,
        googleConsentModeDefault: {
            'ad_storage': 'denied',
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'analytics_storage': 'denied',
        },
        loadConfig() {
            let storage = localStorage.getItem(this.name);
            try {
                storage = JSON.parse(storage);
            } catch {
                storage = null;
            }
            if (storage !== null && this.version === this.version) {
                this.date = storage.date;
                Object.keys(storage.cookies).forEach((item) => {
                    if (this.cache[item] !== undefined) {
                        this.cache[item] = storage.cookies[item];
                    }
                });
                this.valid = Object.assign({}, this.cache);
            }
            setTimeout(() => this.updateGoogleConsentMode(), 200);
        },
        saveConfig() {
            this.valid = Object.assign({}, this.cache);
            localStorage.setItem(this.name, JSON.stringify({
                id: this.id,
                version: this.version,
                date: new Date().getTime(),
                cookies: this.valid,
            }));
            this.closeBanner();
            this.updateGoogleConsentMode();
        },
        accept(cookie) {
            if(!this.cache[cookie]) {
                this.switchCookie(cookie);
                this.saveConfig();
            }
        },
        acceptAll() {
            Object.keys(this.cache).forEach((item) => {
                this.cache[item] = true;
            });
            this.saveConfig();
        },
        saveSettings() {
            this.saveConfig();
        },
        switchCookie(cookie) {
            const group = cookie.split(/[A-Z]/)[0];
            let groupActive = true;
            this.cache[cookie] = !this.cache[cookie];
            Object.keys(this.cache).forEach((item) => {
                if (item !== cookie && item.startsWith(cookie)) {
                    this.cache[item] = this.cache[cookie];
                }
                if (item !== group && item.startsWith(group) && !this.cache[item]) {
                    groupActive = false;
                }
            });
            this.cache[group] = groupActive;
        },
        openBanner() {
            this.details = false;
            this.showBanner = true;
        },
        openHint(content) {
            if(content.text && content.title && content.button && content.callback) {
                this.hint.text = content.text;
                this.hint.title = content.title;
                this.hint.button = content.button;
                this.hint.callback = content.callback;
                this.showHint = true;
            }
        },
        closeBanner() {
            this.showBanner = false;
        },
        acceptHint() {
            this.showHint = false;
            this.hint.callback();
        },
        closeHint() {
            this.showHint = false;
        },
        updateGoogleConsentMode(init = false) {
            const alpine = this;
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                window.dataLayer.push(arguments);
            }

            if (alpine.googleConsentMode) {
                if (init) {
                    const consentDefault = { ...alpine.googleConsentModeDefault };
                    Object.keys(alpine.cache).forEach((item) => {
                        const key = item.match(/[A-Z].*$/);
                        if (key) {
                            consentDefault[key[0].toLowerCase() + '_storage'] = 'denied';
                        }
                    });
                    gtag('consent', 'default', consentDefault);
                } else {
                    const consentUpdate = { ...alpine.googleConsentModeDefault };
                    Object.keys(alpine.cache).forEach((item) => {
                        if (item === 'marketing' && alpine.cache['marketing']) {
                            consentUpdate['ad_storage'] = 'granted';
                            consentUpdate['ad_user_data'] = 'granted';
                            consentUpdate['ad_personalization'] = 'granted';
                        }
                        if (item === 'trackingAnalytics' && alpine.cache['trackingAnalytics']) {
                            consentUpdate['analytics_storage'] = 'granted';
                        }
                        const key = item.match(/[A-Z].*$/);
                        if (key) {
                            consentUpdate[key[0].toLowerCase() + '_storage'] = alpine.cache[item] ? 'granted' : 'denied';
                        }
                    });
                    gtag('consent', 'update', consentUpdate);
                    gtag({ 'event': 'cookie_consent_update' });
                }
            }
        },
    });

    // eslint-disable-next-line no-undef
    Alpine.data('cokibanBanner', () => ({
        get store() {
            return Alpine.store('cokiban');
        },
        showDetails() {
            return this.store.details;
        },
        showOverview() {
            return !this.store.details;
        },
        bindCokiban: {
            'data-x-bind:class'() {
                return { 'cokiban--show-banner': this.store.showBanner, 'cokiban--show-hint': this.store.showHint };
            },
            'data-x-init'() {
                const config = this.$el.dataset.cokibanConfig.split(',');
                this.store.id = config[0];
                this.store.name = 'cokiban_store_' + config[0];
                this.store.version = config[1];
                this.store.days = config[2];
                this.store.active = config[3] === '1';
                this.store.googleConsentMode = config[4] === '1';
                this.store.labels.switchTrue = this.$el.dataset.cokibanLabelSwitchTrue;
                this.store.labels.switchFalse = this.$el.dataset.cokibanLabelSwitchFalse;
                const cookies = this.$el.dataset.cokibanCookies.split(',');
                if (Array.isArray(cookies)) {
                    cookies.forEach((item) => this.store.cache[item] = false);
                }
                this.store.updateGoogleConsentMode(true);
                this.store.valid = Object.assign({}, this.store.cache);
                this.store.loadConfig();
                if (this.store.active === false) {
                    return;
                }
                if (this.store.date === null || (this.store.days && (this.store.date + this.store.days * 86400000) < new Date().getTime())) {
                    this.store.openBanner();
                }
            },
        },
        bindDetailsButton: {
            'data-x-on:click.prevent'() {
                this.store.details = true;
            },
            'data-x-show'() {
                return !this.store.details;
            },
        },
        bindBackButton: {
            'data-x-on:click.prevent'() {
                this.store.details = false;
            },
            'data-x-show'() {
                return this.store.details;
            },
        },
        bindSwitch: {
            'data-x-on:change'(event) {
                this.store.switchCookie(event.target.dataset.cokibanCookie);
            },
            'data-x-bind:checked'() {
                return this.store.cache[this.$el.dataset.cokibanCookie];
            },
        },
        bindSwitchLabel: {
            'data-x-text'() {
                return this.store.cache[this.$el.dataset.cokibanCookie] ? this.store.labels.switchTrue : this.store.labels.switchFalse;
            },
        },
        bindSaveSettings: {
            'data-x-on:click'() {
                this.store.saveSettings();
            },
        },
        bindAcceptAll: {
            'data-x-on:click'() {
                this.store.acceptAll();
            },
        },
        bindAcceptHint: {
            'data-x-on:click'() {
                this.store.acceptHint();
            },
            'data-x-bind:title'() {
                return this.store.hint.title;
            },
            'data-x-text'() {
                return this.store.hint.button;
            },
        },
        bindCloseHint: {
            'data-x-on:click'() {
                this.store.closeHint();
            },
        },
        bindHint: {
            'data-x-text'() {
                return this.store.hint.text;
            },
        },
    }));
    Alpine.data('cokibanButton', () => ({
        get store() {
            return Alpine.store('cokiban');
        },
        bind: {
            'data-x-on:click.prevent'() {
                this.store.openBanner();
            },
        },
    }));
    Alpine.data('cokibanTemplate', () => ({
        get store() {
            return Alpine.store('cokiban');
        },
        bind: {
            'data-x-if'() {
                return this.$el.dataset.cokibanCookies.split(',').find((item) => this.store.valid[item]);
            },
        },
    }));
    Alpine.data('cokibanReplacement', () => ({
        get store() {
            return Alpine.store('cokiban');
        },
        bind: {
            'data-x-if'() {
                return !this.$el.dataset.cokibanCookies.split(',').find((item) => this.store.valid[item]);
            },
        },
    }));
});
