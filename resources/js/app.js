import './bootstrap'
import './../css/app.css'

import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'

import { createPopper } from "@popperjs/core"
import tippy from 'tippy.js'
import Choices from 'choices.js'

import.meta.glob([
    '../images/**',
    '../fonts/**',
]);

window.Alpine = Alpine

/* Alpine.js */
document.addEventListener("alpine:init", () => {

    /* Dark mode */
    Alpine.store("darkMode", {
        on: Alpine.$persist(false).as("darkMode"),
        toggle() {
            this.on = !this.on
            window.location.reload()
        },
    })

    if (Alpine.store("darkMode").on) {
        document.documentElement.classList.add("dark")
    } else {
        document.documentElement.classList.remove("dark")
    }

    /* Popper Dropdown */
    Alpine.data('dropdown', () => ({
        open: false,
        popperInstance: null,
        dropdownBtn: null,
        dropdownBody: null,
        visibilityClasses: ['pointer-events-auto', 'visible', 'opacity-100'],

        init() {
            this.dropdownBtn = this.$refs.dropdownEl.querySelector(".dropdown-btn")
            this.dropdownBody = this.$refs.dropdownEl.querySelector(".dropdown-body")

            const dropdownPlacement = this.$refs.dropdownEl.dataset.dropdownPlacement;

            this.popperInstance = createPopper(this.dropdownBtn, this.dropdownBody, {
                placement: dropdownPlacement ? dropdownPlacement : "auto",
                modifiers: [
                    {
                        name: "offset",
                        options: {
                            offset: [0, 6],
                        },
                    },
                    {
                        name: "flip",
                        options: {
                            allowedAutoPlacements: ["right", "left", "top", "bottom"],
                            rootBoundary: "viewport",
                        },
                    },
                ],
            })
        },

        toggleDropdown() {
            this.open = !this.open
            this.visibilityClasses.forEach(cssClass => this.dropdownBody.classList.toggle(cssClass))
            this.popperInstance.update()
        },

        closeDropdown() {
            this.open = false
            this.visibilityClasses.forEach(cssClass => this.dropdownBody.classList.remove(cssClass))
        }
    }))

    /* Modal */
    Alpine.data('modal', () => ({
        open: false,

        init() {
            Alpine.bind('dismissModal', () => ({
                '@click.outside'() {
                    this.open = false
                },
                '@keydown.escape.window'() {
                    this.open = false
                }
            }))
        },

        toggleModal() {
            this.open = !this.open
        },
    }))

    /* Offcanvas */
    Alpine.data('offcanvas', () => ({
        open: false,

        init() {
            Alpine.bind('dismissCanvas', () => ({
                '@click.outside'() {
                    this.open = false
                },
                '@keydown.escape.window'() {
                    this.open = false
                }
            }))
        },

        toggleCanvas() {
            this.open = !this.open
        },
    }))

    /* Select */
    Alpine.data('select', () => ({
        ref: null,
        choicesInstance: null,
        placeholder: null,
        searchEnabled: null,
        removeItemButton: null,

        init() {
            this.ref = this.$refs.select
            this.placeholder = this.ref.getAttribute('placeholder')
            this.searchEnabled = !!this.ref.dataset.searchEnabled
            this.removeItemButton = !!this.ref.dataset.removeItemButton
            this.choicesInstance = new Choices(this.ref, {
                allowHTML: true,
                position: 'bottom',
                placeholderValue: this.placeholder,
                searchEnabled: this.searchEnabled,
                removeItemButton: this.removeItemButton,
            })
        },
    }))

    /* Tooltip */
    Alpine.data('tooltip', (text, config = {}) => ({
        tooltipInstance: null,
        init() {
            this.tooltipInstance = tippy(this.$el, { ...config, content: text })
        },
    }))

    /* Aside Navigation Tooltip */
    Alpine.data('navTooltip', () => ({
        tooltipInstance: null,
        init() {
            this.tooltipInstance = tippy(this.$el, {
                placement: 'right',
                offset: [0, 30],
                content: () => this.$el.querySelector('.menu-inner-text').textContent,
            })
        },
        toggleTooltip() {
            const lgMediaQuery = window.matchMedia('(min-width: 1024px) and (max-width: 1279.98px)');

            if (!this.$data.minimizedMenu && !lgMediaQuery.matches) {
                this.tooltipInstance.hide()
            }
        },
    }))

    Alpine.data('pivot', () => ({
        autoCheck() {
            let checker = this.$root.querySelector('.pivotChecker')
            let fields = this.$root.querySelectorAll('.pivotFields')

            fields.forEach(function (value, key) {
                value.addEventListener('input', (event) => {
                    checker.checked = event.target.value
                });
            })
        }
    }))

    Alpine.data('search', (route, resourceUri, column) => ({
        items: [],
        match: [],
        query: '',
        select(index) {
            if (!this.items.includes(this.match[index])) {
                this.items.push({ key: index, value: this.match[index] })
            }

            this.query = ''
            this.match = []
        },
        async search() {
            if (this.query.length > 2) {
                let query = '?query=' + this.query + '&resource=' + resourceUri + '&column=' + column;

                fetch(route + query).then((response) => {
                    return response.json();
                }).then((data) => {
                    this.match = data
                })
            }
        },
    }))

    Alpine.data('asyncData', () => ({
        async load(url, id) {
            const { data, status } = await axios.get(url);

            if (status === 200) {
                let containerElement = document.getElementById(id)

                containerElement.innerHTML = data

                const scriptElements = containerElement.querySelectorAll("script");

                Array.from(scriptElements).forEach((scriptElement) => {
                    const clonedElement = document.createElement("script");

                    Array.from(scriptElement.attributes).forEach((attribute) => {
                        clonedElement.setAttribute(attribute.name, attribute.value);
                    });

                    clonedElement.text = scriptElement.text;

                    scriptElement.parentNode.replaceChild(clonedElement, scriptElement);
                });
            }
        },
        async updateColumn(route, column, localKey, className, value) {
            const response = await axios.put(route, {
                value: value,
                key: localKey,
                model: className,
                field: column
            });

            if (response.status === 204) {
                //
            }

            if (response.status === 422) {
                //
            }
        }
    }))

    Alpine.data('crudForm', () => ({
        whenFields: {},
        init(whenFields) {
            this.whenFields = whenFields
        },
        precognition(form) {
            form.querySelector('.form_submit_button').setAttribute('disabled', 'true');
            form.querySelector('.form_submit_button').innerHTML = translates.loading;
            form.querySelector('.precognition_errors').innerHTML = '';

            axios.post(form.getAttribute('action'), new FormData(form), {
                headers: {
                    Precognition: true,
                    Accept: 'application/json',
                    'Content-Type': form.getAttribute('enctype')
                }
            }).then(function (response) {
                form.submit()
            }).catch(errorResponse => {
                form.querySelector('.form_submit_button').innerHTML = translates.saved_error;
                form.querySelector('.form_submit_button').removeAttribute('disabled');

                let errors = '';
                let errorsData = errorResponse.response.data.errors;
                for (const error in errorsData) {
                    errors = errors + '<div class="mt-2 text-pink">' + errorsData[error] + '</div>';
                }

                form.querySelector('.precognition_errors').innerHTML = errors;
            });

            return false;
        },
    }))

    Alpine.data('crudTable', () => ({
        actionsOpen: false,
        actions(type) {
            let all = this.$root.querySelector('.actionsAllChecked');

            if (all === null) {
                return;
            }

            let checkboxes = this.$root.querySelectorAll('.tableActionRow');
            let ids = document.querySelectorAll('.actionsCheckedIds');

            let values = [];

            for (let i = 0, n = checkboxes.length; i < n; i++) {
                if (type === 'all') {
                    checkboxes[i].checked = all.checked;
                }

                if (checkboxes[i].checked && checkboxes[i].value) {
                    values.push(checkboxes[i].value);
                }
            }

            for (let i = 0, n = ids.length; i < n; i++) {
                ids[i].value = values.join(";");
            }

            this.actionsOpen = !!(all.checked || values.length);
        }
    }))
})

Alpine.plugin(persist)
Alpine.plugin(mask)
Alpine.start()
