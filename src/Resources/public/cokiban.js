import Alpine from 'alpinejs'

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
      this.id = config.id
      this.name = config.name
      this.version = config.version
      this.days = config.days
      this.active = config.active
      if (Array.isArray(config.cookies)) {
        config.cookies.forEach((item) => {
          this.cache[item] = false
        })
      }
      this.valid = Object.assign({}, this.cache)
      this.loadConfig()
      if (this.active === false) {
        return
      }
      if (this.date === null || (this.days && (this.date + this.days * 86400000) < new Date().getTime())) {
        this.openBanner()
      }
    },
    loadConfig () {
      let storage = localStorage.getItem(this.name)
      try {
        storage = JSON.parse(storage)
      } catch {
        storage = null
      }
      if (storage !== null && storage.version === this.version) {
        this.date = storage.date
        Object.keys(storage.cookies).forEach((item) => {
          if (this.cache[item] !== undefined) {
            this.cache[item] = storage.cookies[item]
          }
        })
        this.valid = Object.assign({}, this.cache)
      }
    },
    saveConfig () {
      this.valid = Object.assign({}, this.cache)
      localStorage.setItem(this.name, JSON.stringify({
        id: this.id,
        version: this.version,
        date: new Date().getTime(),
        cookies: this.valid
      }))
      this.closeBanner()
    },
    acceptAll () {
      Object.keys(this.cache).forEach((item) => {
        this.cache[item] = true
      })
      this.saveConfig()
    },
    showDetails () {
      this.details = !this.details
    },
    saveSettings () {
      this.saveConfig()
    },
    switchCookie (cookie) {
      const group = cookie.split(/[A-Z]/)[0]
      let groupActive = true
      this.cache[cookie] = !this.cache[cookie]
      Object.keys(this.cache).forEach((item) => {
        if (item !== cookie && item.startsWith(group)) {
          this.cache[item] = this.cache[cookie]
        }
        if (item !== group && item.startsWith(group) && !this.cache[item]) {
          groupActive = false
        }
      })
      this.cache[group] = groupActive
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
      'data-x-on:click' () {
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
