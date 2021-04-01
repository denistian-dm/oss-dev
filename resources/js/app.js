require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue3';

import axios from 'axios';
import VueAxios from 'vue-axios';

import { VTooltip, VPopover, VClosePopover } from 'v-tooltip';

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const el = document.getElementById('app');

createApp({
    render: () =>
        h(InertiaApp, {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => require(`./Pages/${name}`).default,
        }),
})
    .mixin({ methods: { route } })
    .use(InertiaPlugin, VueAxios, axios)
    .use(VueSweetalert2)
    .directive('tooltip', VTooltip)
    .directive('close-popover', VClosePopover)
    .component('v-popover', VPopover)
    .mount(el);
