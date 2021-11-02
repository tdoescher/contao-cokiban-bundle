document.addEventListener('alpine:init', function () {
    Alpine.store('cokiban', {
        id: null,
        name: '',
        version: null,
        days: null,
        active: true,
        date: null,
        show: false,
        details: false,
        cache: {},
        valid: {},
        initialize(config) {
            let alpine = this;
            alpine.id = config.id;
            alpine.name = config.name;
            alpine.version = config.version;
            alpine.days = config.days;
            alpine.active = config.active;
            config.cookies.forEach(function(item) {
                alpine.cache[item] = false;
            });
            alpine.valid = { ...alpine.cache };
            alpine.pages = config.pages;
            alpine.load();
            if(alpine.active === false) {
                return;
            }
            if(alpine.date === null || (alpine.days && (alpine.date + alpine.days * 86400000) < new Date().getTime())) {
                alpine.openBanner();
            }
        },
        load() {
            let alpine = this;
            let storage = localStorage.getItem(alpine.name);
            try {
                storage = JSON.parse(storage);
            } catch {
                storage = null;
            }
            if(storage !== null && storage.version === alpine.version) {
                alpine.date = storage.date;
                Object.keys(storage.cookies).forEach(function(item) {
                    if(alpine.cache[item] !== undefined) {
                        alpine.cache[item] = storage.cookies[item];
                    }
                });
                alpine.valid = { ...alpine.cache };
            }
        },
        save() {
            let alpine = this;
            alpine.valid = { ...alpine.cache }
            localStorage.setItem(alpine.name, JSON.stringify({
                id: alpine.id,
                version: alpine.version,
                date: new Date().getTime(),
                cookies: alpine.valid
            }));
            alpine.closeBanner();
        },
        acceptAll() {
            let alpine = this;
            Object.keys(alpine.cache).forEach(function(item) {
                alpine.cache[item] = true;
            });
            alpine.save();
        },
        saveSettings() {
            let alpine = this;
            alpine.save();
        },
        switchCookie(cookie) {
            let alpine = this;
            let group = cookie.split(/[A-Z]/)[0];
            let groupActive = true;
            Object.keys(alpine.cache).forEach(function(item) {
                if(item !== cookie && item.startsWith(cookie)) {
                    alpine.cache[item] = alpine.cache[cookie];
                }
                if(item !== group && item.startsWith(group) && !alpine.cache[item]) {
                    groupActive = false;
                }
            });
            alpine.cache[group] = groupActive;
        },
        showDetails() {
            let alpine = this;
            alpine.details = ! this.details;
        },
        openBanner() {
            let alpine = this;
            alpine.details = false;
            alpine.show = true;
        },
        closeBanner() {
            let alpine = this;
            alpine.show = false;
        }
    });
});
