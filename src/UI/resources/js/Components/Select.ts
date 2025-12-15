import TomSelect from 'tom-select'
import type { RecursivePartial, TomSettings } from 'tom-select/src/types'
import { TPluginHash } from 'tom-select/src/contrib/microplugin'

import { crudFormQuery, getQueryString, prepareFormExtraData } from '../Support/Forms.js'
import { dispatchEvents as de } from '../Support/DispatchEvents.js'
import { formToJSON } from 'axios'
import { getDom } from 'tom-select/src/vanilla'

let pluginInitialize = false

type UserSettings = RecursivePartial<TomSettings> & Record<string, any>


const allTranslates: Record<string, string> = typeof translates === 'object' ? (translates.select || {}) : {}

const MaxItemsPlugin = TomSelect.define('max_items', function (pluginOptions = {}) {
    if (! this.settings.maxItems) {
        return
    }

    const options = Object.assign({
        className: 'max-items-message',
        text: null,
        render: (data) => {
            const text = (data.text || allTranslates.max_item).replace(':count', this.settings.maxItems)
            return `<div class="${data.className}">${ text }</div>`;
        }
    }, pluginOptions);

    const orig_open = this.open;
    this.hook('instead', 'open', function() {
        let origMode = this.settings.mode
        if (this.isFull()) {
            this.settings.mode = 'single'
        }

        const result = orig_open.call(this)
        this.settings.mode = origMode
        return result
    })

    this.hook('after', 'refreshOptions', function() {
        if (this.isFull()) {
            this.dropdown_content.innerHTML = ''
            this.dropdown_content.insertBefore(getDom(options.render(options)), this.dropdown_content.firstChild);
        }
    })

    this.hook('after', 'addItem', function() {
        if (this.isSetup && this.isFull()) {
            this.open()
        }
    })

    this.on('clear', function () {
        this.close()
    })
})


export default (asyncUrl = '', settings: UserSettings = {}, plugins: TPluginHash = {}) => ({
    selectInstance: null,
    isMultiple: false,
    placeholder: null,
    searchEnabled: null,
    removeItemButton: null,
    associatedWith: null,
    morphClearValue: '',

    isLoadedOptions: false,

    translates: allTranslates,

    init() {
        if (! pluginInitialize) {
            document.dispatchEvent(new CustomEvent('moonshine:select_init', {
                detail: {
                    createPlugin: TomSelect.define
                }
            }))
            pluginInitialize = true
        }

        const _this = this
        const commonPlugins: Record<string, Record<string, any>> = {}

        this.isMultiple = this.$el.hasAttribute('multiple') || settings.mode === 'multi'
        this.placeholder = this.$el.getAttribute('placeholder')
        this.searchEnabled = !! this.$el.dataset.searchEnabled
        this.removeItemButton = !! this.$el.dataset.removeItemButton
        this.associatedWith = this.$el.dataset.associatedWith

        if (this.associatedWith) {
            this.$el.removeAttribute('data-associated-with')
        }

        if (this.isMultiple) {
            commonPlugins.remove_button = {
                title: allTranslates.remove_item
            }
        }

        if (this.removeItemButton) {
            commonPlugins.clear_button = {
                title: allTranslates.clear_all
            }
        } else {
            commonPlugins.no_backspace_delete = {}
        }

        if (settings.createFilter && typeof settings.createFilter === 'object' && settings.createFilter.pattern) {
            const createFilterRegex = new RegExp(settings.createFilter.pattern, settings.createFilter.modifiers)
            settings.createFilter = function(input: string) {
                return createFilterRegex.test(input) && (this.settings.duplicates || ! this.options[input])
            }
        }

        if (settings.splitOn && typeof settings.splitOn === 'object' && settings.splitOn.pattern) {
            settings.splitOn = new RegExp(settings.splitOn.pattern, settings.splitOn.modifiers)
        }

        const commonSettings: UserSettings = {
            plugins: {
                ...commonPlugins,
                ...plugins
            },
            mode: this.isMultiple ? 'multi' : 'single',
            allowEmptyOption: ! asyncUrl && ! this.isMultiple,
            // hidePlaceholder: true,
            placeholder: this.placeholder,
            loadThrottle: 300,

            optgroupValueField: 'value',
            optgroupLabelField: 'label',
            optgroupField: 'optgroup',

            valueField: 'value',
            labelField: 'label',
            descriptionField: 'description',
            // imageField: 'image',
            childrenField: 'values',

            disabledField: 'disabled',
            searchField: [ 'label' ],
            dataAttr: 'customProperties',
            dropdownParent: 'body',

            shouldOpen: ! asyncUrl,

            ...settings,

            load: asyncUrl
                ? query => this.asyncSearch(query)
                : null,

            onDropdownOpen(dropdown_content) {
                if (! asyncUrl && ! this.settings.mode === 'single' && ! this.items.length) {
                    let content = this.render('no_results', { input: '', no_options: true })
                    dropdown_content.innerHTML = content.outerHTML
                }
            },

            render: {
                option(data, escape) {
                    const label = escape(data[this.settings.labelField])
                    const description = data[this.settings.descriptionField]
                        ? escape(data[this.settings.descriptionField])
                        : null
                    const { image } = (data.customProperties || {})

                    return `<div class="flex gap-x-2 items-center ${ image ? 'with-image' : '' }">
                                ${ _this.imageRender(image) }
                                <div class="flex flex-col gap-2">
                                    <div>${ label }</div>
                                    ${ description ? `<div class="text-gray-400">${ description }</div>` : '' }
                                </div>
                            </div>`
                },
                item(data, escape) {
                    const label = escape(data[this.settings.labelField])
                    const { image } = (data.customProperties || {})

                    return `<div class="${ image ? 'with-image' : '' }">
                                ${ _this.imageRender(image) }
                                <div>${ label }</div>
                            </div>`
                },
                option_create: function (data, escape) {
                    const text = allTranslates.add_item.replace(':value', `<strong>${ escape(data.input) }</strong>`)
                    return `<div class="create">${ text }&hellip;</div>`
                },
                no_results(data, escape) {
                    let text

                    const isCreate = this.settings.create && data.input.length > 0

                    if (data.no_options) {
                        text = allTranslates.no_options
                    } else if (isCreate && ! this.settings.duplicates && this.options[data.input]) {
                        text = allTranslates.unique_item
                    } else if (isCreate && !! settings.createFilter && ! this.canCreate(data.input)) {
                        text = allTranslates.custom_add_item
                    } else {
                        text = allTranslates.no_results.replace(':value', escape(data.input))
                    }

                    return `<div class="no-results">${ text }</div>`
                },
            }
        }

        if (! this.searchEnabled) {
            commonSettings.controlInput = null
        }

        this.selectInstance = new TomSelect(this.$el, commonSettings)

        this.$nextTick(() => {
            this.setClassSelectedEmptyValue.call(this.selectInstance)
            this.selectInstance.on('change', function (value) {
                _this.setClassSelectedEmptyValue.call(this, value)
            })

            this.asyncOnInit()

            if (this.associatedWith && asyncUrl) {
                document.querySelector(`[name="${ this.associatedWith }"]`).addEventListener(
                    'change',
                    e => {
                        this.selectInstance.clear()
                        this.selectInstance.trigger('change')
                        this.isLoadedOptions = false
                    },
                    false
                )
            }
        })
    },

    asyncOnInit() {
        // Loading immediately after "Initialization"
        if (! asyncUrl || ! this.$el.dataset.asyncOnInit) {
            return
        }

        if (this.$el.dataset.asyncOnInitDropdown) {
            const asyncOnInitFocusCb = () => {
                if (! this.isLoadedOptions) {
                    this.$el.dataset.asyncOnInitDropdownWithLoading
                        ? this.selectInstance.load('')
                        : this.asyncSearch()
                }

                this.selectInstance.off('focus', asyncOnInitFocusCb)
            }
            this.selectInstance.on('focus', asyncOnInitFocusCb)
            return
        }

        this.selectInstance.preload()
    },

    async asyncSearch(query: string | null = null) {
        let canRequest = this.$el.dataset.asyncOnInit || (query !== null && query.length > 0)
        let options = []
        let optgroups = []
        let selected = []

        if (canRequest) {
            const url = new URL(
                asyncUrl,
                asyncUrl.startsWith('/')
                    ? window.location.origin
                    : undefined
            )

            url.searchParams.append(this.$el.dataset.asyncQueryKey || 'query', query || '')

            const form = this.$el.form
            const inputs = form ? form.querySelectorAll('[name]') : []
            const value = this.selectInstance.getValue()

            let formQuery = this.$el.dataset.asyncSelectedValuesKey
                ? getQueryString({ [this.$el.dataset.asyncSelectedValuesKey]: value || '' })
                : ''

            if (this.$el.dataset.asyncWithAllFields && inputs.length) {
                formQuery += (formQuery ? '&' : '') + crudFormQuery(inputs)
            }

            options = await this.fetchOptions(url.toString() + '&' + formQuery)
            const normalizeOptions = this.normalizeOptions(options)

            optgroups = normalizeOptions.groups
            options = normalizeOptions.options
            selected = normalizeOptions.selected
        }

        this.selectInstance.loadCallback(options, optgroups)
        if (selected.length) {
            this.$nextTick(() => {
                let isOpen = this.selectInstance.isOpen
                this.selectInstance.setValue(selected)

                if (isOpen) {
                    this.selectInstance.open()
                }
            })
        }

        this.isLoadedOptions = true
    },
    async fetchOptions(url: string) {
        let options = []
        try {
            const response = await fetch(url)
            options = await response.json()
            if (this.$el.dataset.asyncResultKey) {
                options = options[this.$el.dataset.asyncResultKey]
            }
        } catch (e) {
        }

        return options
    },
    normalizeOptions(items): { options: Record<string, any>, groups: Record<string, any>, selected: (string|number)[] } {
        const {
            optgroupValueField, optgroupLabelField,
            childrenField, disabledField
        } = this.selectInstance.settings

        const options = []
        const groups = []
        const selected = []

        for (const key in items) {
            let item = items[key]

            // If groups are transmitted in the form
            // { "groupName": [...], "groupsName2": [...], .. }
            if (! /^\d+$/g.test(key) && typeof item !== 'string') {
                item = {
                    [optgroupLabelField]: key,
                    [childrenField]: item
                }
            }

            // If there are child elements, then we format them as Groups.
            if (item.hasOwnProperty(childrenField)) {
                let groupOptions = item[childrenField]
                delete item[childrenField]

                let groupData = item

                const group = {
                    [optgroupValueField]: JSON.stringify(groupData[optgroupLabelField]),
                    [disabledField]: !! groupData[disabledField],
                    ...groupData
                }
                groups.push(group)

                for (let key in groupOptions) {
                    options.push(this.normalizeOption(groupOptions[key], key, group[optgroupValueField], selected))
                }

                continue
            }

            options.push(this.normalizeOption(item, key, undefined, selected))
        }

        return { options, groups, selected }
    },
    normalizeOption(option, key, group, selected): Record<string, any> {
        // If it is passed as an object, then we normalize it
        // { "value": "Label", "value_2": "Label 2", ... }
        if (typeof option === 'string') {
            option = {
                [this.selectInstance.settings.valueField]: key,
                [this.selectInstance.settings.labelField]: option
            }
        }

        if (group) {
            option[this.selectInstance.settings.optgroupField] = group
        }

        if (option.selected) {
            selected.push(option[this.selectInstance.settings.valueField])
        }

        const { properties, ...rest } = option

        return {
            ...rest,
            [this.selectInstance.settings.disabledField]: !! rest[this.selectInstance.settings.disabledField],
            customProperties: Array.isArray(properties) ? {} : properties || {}
        }
    },

    imageRender(image): string {
        let result = ''
        if (image) {
            const imageData = this.normalizeImageData(image)
            result = `<div class="zoom-in overflow-hidden rounded-md h-${ imageData.height } w-${ imageData.width }">
                            <img src="${ imageData.src }" class="h-full w-full object-${ imageData.objectFit }" alt="" />
                        </div>`
        }

        return result
    },

    normalizeImageData(image: string | Record<string, any>): Record<string, any> {
        if (typeof image === 'string') {
            image = { src: image }
        }

        return {
            width: 10,
            height: 10,
            objectFit: 'cover',
            ...image
        }
    },

    setClassSelectedEmptyValue(value) {
        if (this.settings.mode === 'single' && this.settings.allowEmptyOption) {
            if (! arguments.length) {
                value = this.getValue()
            }

            this.wrapper.classList.toggle('selected-empty-value', value === '')
        }
    },


    dispatchEvents(componentEvent, exclude = null, extra = {}) {
        const form = this.$el.closest('form')

        if (exclude !== '*') {
            extra['_data'] = form
                ? formToJSON(prepareFormExtraData(new FormData(form), exclude))
                : { value: this.selectInstance.getValue() }
        }

        de(componentEvent, '', this, extra)
    },

    morphClear(type: string) {
        if (type && this.morphClearValue !== type) {
            this.selectInstance.clear(true)
            this.selectInstance.clearOptions()
            this.asyncSearch()
            this.morphClearValue = type
        }
    },
})
