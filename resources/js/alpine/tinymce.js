/* Tinymce */

export default (config = {}) => ({
  config: config,

  async init() {
    await this.$nextTick()

    const fileManager = function (callback, value, meta) {
      const tinyConfig = config(Alpine.store('darkMode').on)
      const x =
        window.innerWidth ||
        document.documentElement.clientWidth ||
        document.getElementsByTagName('body')[0].clientWidth
      const y =
        window.innerHeight ||
        document.documentElement.clientHeight ||
        document.getElementsByTagName('body')[0].clientHeight
      const cmsURL =
        tinyConfig.path_absolute +
        tinyConfig.file_manager +
        '?editor=' +
        meta.fieldname +
        (meta.filetype === 'image' ? '&type=Images' : '&type=Files')

      tinyMCE.activeEditor.windowManager.openUrl({
        url: cmsURL,
        title: 'File Manager',
        width: x * 0.8,
        height: y * 0.8,
        resizable: 'yes',
        close_previous: 'no',
        onMessage: (api, message) => callback(message.content),
      })
    }

    let editorInstance = null

    const config = darkMode => ({
      selector: '#' + this.$el.getAttribute('id'),
      path_absolute: '/',
      file_manager: '',
      relative_urls: false,
      branding: false,
      skin: darkMode ? 'oxide-dark' : 'oxide',
      content_css: darkMode ? 'dark' : 'default',
      ...this.config,
      file_picker_callback: this.config.file_manager ? fileManager : null,
      init_instance_callback: editor =>
        editor.on('blur', () => (this.$el.innerHTML = editor.getContent())),
      setup: editor => {
        editorInstance = editor
      },
    })

    tinymce.init(config(Alpine.store('darkMode').on))

    window.addEventListener('darkMode:toggle', () => tinymce.remove(editorInstance))
  },
})
