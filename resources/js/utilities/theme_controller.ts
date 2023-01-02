export const themeController = {
    themeLSKey: 'shine-theme',

    get isDark(){
        return this.getCurrent() === 'dark'
    },
    getCurrent() {
        //todo: light mode. now light = system

        if (localStorage.getItem(this.themeLSKey) === 'dark' || (!(this.themeLSKey in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            return 'dark'
        } else {
            return 'system'
        }
    },
    setupTheme() {
        if (this.getCurrent() === 'dark')
            document.documentElement.classList.add('dark')
        else
            document.documentElement.classList.remove('dark')
    },
    set(theme: 'dark' | 'system') {
        switch (theme) {
            case "dark":
                localStorage.setItem(this.themeLSKey, theme)
                break
            case "system":
                localStorage.removeItem(this.themeLSKey)
        }
        this.setupTheme()
    },
    setDark() {
        this.set('dark')
    },
    setSystem() {
        this.set('system')
    },
    switch() {
        if (this.isDark)
            this.setSystem()
        else
            this.setDark()
    }
}
