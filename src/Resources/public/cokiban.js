document.addEventListener('alpine:init', () => {
  Alpine.prefix('data-x-')
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
    initialize (config) {
      const alpine = this
      alpine.id = config.id
      alpine.name = config.name
      alpine.version = config.version
      alpine.days = config.days
      alpine.active = config.active
      if (Array.isArray(config.cookies)) {
        config.cookies.forEach((item) => {
          alpine.cache[item] = false
        })
      }
      alpine.valid = Object.assign({}, alpine.cache);
      alpine.pages = config.pages;
      alpine.loadConfig()
      if (alpine.active === false) {
        return
      }
      if (alpine.date === null || (alpine.days && (alpine.date + alpine.days * 86400000) < new Date().getTime())) {
        alpine.openBanner();
      }
    },
    loadConfig () {
      const alpine = this
      let storage = localStorage.getItem(alpine.name)
      try {
        storage = JSON.parse(storage)
      } catch {
        storage = null
      }
      if (storage !== null && storage.version === alpine.version) {
        alpine.date = storage.date
        Object.keys(storage.cookies).forEach((item) => {
          if (alpine.cache[item] !== undefined) {
            alpine.cache[item] = storage.cookies[item]
          }
        });
        alpine.valid = Object.assign({}, alpine.cache)
      }
    },
    saveConfig () {
      const alpine = this
      alpine.valid = Object.assign({}, alpine.cache)
      localStorage.setItem(alpine.name, JSON.stringify({
        id: alpine.id,
        version: alpine.version,
        date: new Date().getTime(),
        cookies: alpine.valid
      }));
      alpine.closeBanner()
    },
    acceptAll () {
      const alpine = this
      Object.keys(alpine.cache).forEach((item) => {
        alpine.cache[item] = true
      })
      clearInterval(this.counter)
      alpine.saveConfig()
    },
    showDetails () {
      this.details = !this.details
    },
    saveSettings () {
      this.saveConfig()
    },
    switchCookie (cookie) {
      const alpine = this
      const group = cookie.split(/[A-Z]/)[0]
      let groupActive = true
      alpine.cache[cookie] = !alpine.cache[cookie]
      Object.keys(alpine.cache).forEach((item) => {
        if (item !== cookie && item.startsWith(cookie)) {
          alpine.cache[item] = alpine.cache[cookie]
        }
        if (item !== group && item.startsWith(group) && !alpine.cache[item]) {
          groupActive = false
        }
      })
      alpine.cache[group] = groupActive
    },
    openBanner () {
      this.details = false
      this.show = true
    },
    closeBanner () {
      this.show = false
    },
    bindCokiban: {
      'data-x-bind:class' () {
        return { 'cokiban--show': this.show }
      }
    },
    bindDetails: {
      'data-x-on:click.prevent' () {
        this.showDetails()
      }
    },
    bindSwitch: {
      'data-x-on:change' (event) {
        this.switchCookie(event.target.dataset.cookie)
      }
    },
    bindSaveSettings: {
      'data-x-on:click' () {
        this.saveSettings()
      }
    },
    bindAcceptAll: {
      'data-x-on:click' () {
        this.acceptAll()
      }
    },
    bindOpenBanner: {
      'data-x-on:click.prevent' () {
        this.$store.cokiban.openBanner()
      }
    }
  })
})
