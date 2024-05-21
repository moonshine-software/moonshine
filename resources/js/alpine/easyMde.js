/* EasyMDE */

export default () => ({
  init() {
    new EasyMDE({
      element: this.$el,
      previewClass: ['prose', 'dark:prose-invert'],
      forceSync: true,
      spellChecker: false,
      status: false,
      renderingConfig: {
        sanitizerFunction: renderedHTML => {
          return DOMPurify.sanitize(renderedHTML, {
            USE_PROFILES: {
              html: true,
            },
          })
        },
      },
      toolbar: [
        'bold',
        'italic',
        'strikethrough',
        'code',
        'quote',
        'horizontal-rule',
        '|',
        'heading-1',
        'heading-2',
        'heading-3',
        '|',
        'table',
        'unordered-list',
        'ordered-list',
        '|',
        'link',
        'image',
        '|',
        'preview',
        'side-by-side',
        'fullscreen',
        '|',
        'guide',
      ],
    })
  },
})
