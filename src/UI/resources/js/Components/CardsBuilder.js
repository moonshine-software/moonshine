import {listComponentRequest} from '../Request/Sets.js'
import {urlWithQuery} from '../Request/Core.js'
import {crudFormQuery} from '../Support/Forms.js'

export default (async = false, asyncUrl = '') => ({
  actionsOpen: false,
  async: async,
  asyncUrl: asyncUrl,
  loading: false,
  // Set by listComponentRequest(): aborted when a newer list request starts
  listRequestController: null,
  init() {},
  asyncRequest() {
    listComponentRequest(this, this.$root?.dataset?.pushState)
  },
  asyncFormRequest() {
    this.asyncUrl = urlWithQuery(
      this.$el.getAttribute('action'),
      crudFormQuery(this.$el.querySelectorAll('[name]')),
    )

    this.asyncRequest()
  },
})
