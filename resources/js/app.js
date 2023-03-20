import './../css/app.css'

import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'

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

            this.popperInstance = Popper.createPopper(this.dropdownBtn, this.dropdownBody, {
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
            this.visibilityClasses.map(cssClass => this.dropdownBody.classList.toggle(cssClass))
            this.popperInstance.update()
        },

        closeDropdown() {
            this.open = false
            this.visibilityClasses.map(cssClass => this.dropdownBody.classList.remove(cssClass))
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
            this.searchEnabled = this.ref.dataset.searchEnabled ? true : false
            this.removeItemButton = this.ref.dataset.removeItemButton ? true : false
            this.choicesInstance = new Choices(this.ref, {
                allowHTML: true,
                position: 'bottom',
                placeholderValue: this.placeholder,
                searchEnabled: this.searchEnabled,
                removeItemButton: this.removeItemButton,
            })
        },
    }))

    /* CKEditor */
    Alpine.data('ckeditor', () => ({
        ref: null,
        CKEditorInstance: null,

        init() {
            this.ref = this.$refs.ckeditor
            this.choicesInstance = ClassicEditor.create(this.ref)
        },
    }))

    Alpine.data('asyncData', () => ({
        load(url, id) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
            }).then(function (response) {
                return response.text()
            }).then(function (html) {
                let containerElement = document.getElementById(id)

                containerElement.innerHTML = html

                const scriptElements = containerElement.querySelectorAll("script");

                Array.from(scriptElements).forEach((scriptElement) => {
                    const clonedElement = document.createElement("script");

                    Array.from(scriptElement.attributes).forEach((attribute) => {
                        clonedElement.setAttribute(attribute.name, attribute.value);
                    });

                    clonedElement.text = scriptElement.text;

                    scriptElement.parentNode.replaceChild(clonedElement, scriptElement);
                });

            }).catch(function (err) {

            });
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

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Precognition': 'true',
                },
                body: new FormData(form)
            }).then(function (response) {
                if (response.status === 200) {
                    form.submit()
                }

                return response.json();
            }).then(function (json) {
                if (Object.keys(json).length) {
                    form.querySelector('.form_submit_button').innerHTML = translates.saved_error;
                    form.querySelector('.form_submit_button').removeAttribute('disabled');

                    let errors = '';

                    for (const key in json) {
                        errors = errors + '<div class="mt-2 text-pink">' + json[key] + '</div>';
                    }

                    form.querySelector('.precognition_errors').innerHTML = errors;
                }
            })


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
            let checked = this.$root.querySelectorAll('.tableActionRow:checked');
            let ids = this.$root.querySelectorAll('.actionsCheckedIds');

            let values = [];

            for(let i=0, n=checkboxes.length;i<n;i++) {
                if(type === 'all') {
                    checkboxes[i].checked = all.checked;
                }

                if(checkboxes[i].checked && checkboxes[i].value) {
                    values.push(checkboxes[i].value);
                }
            }

            for(let i=0, n=ids.length;i<n;i++) {
                ids[i].value = values.join (";");
            }

            if (all.checked || values.length) {
                this.actionsOpen = true;
            } else {
                this.actionsOpen = false;
            }
        }
    }))
})

Alpine.plugin(persist)
Alpine.plugin(mask)
Alpine.start()


