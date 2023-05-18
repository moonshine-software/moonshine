/* Tinymce */

export default () => ({
    init() {
        const fileManager = function (callback, value, meta) {
            const x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
            const y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight
            const cmsURL = config.path_absolute + config.file_manager + '?editor=' + meta.fieldname + (meta.filetype === 'image' ? '&type=Images' : '&type=Files')

            tinyMCE.activeEditor.windowManager.openUrl({
                url: cmsURL,
                title: 'File Manager',
                width: x * 0.8,
                height: y * 0.8,
                resizable: 'yes',
                close_previous: 'no',
                onMessage: (api, message) => callback(message.content)
            })
        }

        const config = {
            selector: this.$el.getAttribute('id'),
            path_absolute: '/',
            file_manager: '',
            relative_urls: false,
            skin: Alpine.store('darkMode').on ? 'oxide-dark' : 'oxide',
            content_css: Alpine.store('darkMode').on ? 'dark' : 'default',
            ...this.$el.dataset,
            file_picker_callback: this.$el.dataset.file_manager ? fileManager : null,
            init_instance_callback: (editor) => editor.on('blur', () => this.$el.innerHTML = editor.getContent())
        }

        tinymce.init(config)
    },
})
