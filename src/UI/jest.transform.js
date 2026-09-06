import {transformWithOxc} from 'vite'

export default {
  async processAsync(source, filename) {
    return transformWithOxc(source, filename)
  },
}
