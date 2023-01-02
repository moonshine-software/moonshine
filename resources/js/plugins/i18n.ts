import { createI18n } from 'vue-i18n'
import en from '../locales/en.json'
import ru from '../locales/ru.json'

const locale = localStorage.getItem('lang') ?? 'en'
const i18n = createI18n({
    locale,
    messages: {
        en,
        ru,
    },
})

export default i18n
