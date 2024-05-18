/* EasyMDE */

import EasyMDE from 'easymde'
import DOMPurify from 'dompurify'

export default () => ({
  init() {
    this.$nextTick(() => {
      new EasyMDE({
        element: this.$el,
        previewClass: ['prose', 'dark:prose-invert'],
        renderingConfig: {
          sanitizerFunction: (renderedHTML) => {
            return DOMPurify.sanitize(renderedHTML, {
              USE_PROFILES: {
                html: true
              }
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
        ]
      })
    })
  },
})
