import {createApp} from 'vue'
import Moonshine from './Moonshine.vue'
import "./sass/app.scss"
// @ts-ignore
import VueTippy from "./plugins/vue-tippy"
// @ts-ignore
import i18n from "./plugins/i18n"

import Notifications from '@kyvg/vue3-notification'
import {createPinia} from "pinia";
import router from "./router/routes";

const pinia = createPinia()

createApp(Moonshine)
    .use(pinia)
    .use(router)
    .use(i18n)
    .use(VueTippy)
    .use(Notifications)
    .mount('#moonshine')
