import {listComponentRequest} from './asyncFunctions.js'

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
