import {listComponentRequest} from '../Request/Sets.js'

export default (async = false, asyncUrl = '') => ({
  actionsOpen: false,
  async: async,
  asyncUrl: asyncUrl,
  loading: false,
  init() {},
  asyncRequest() {
    listComponentRequest(this, this.$root?.dataset?.pushstate)
  },
})
