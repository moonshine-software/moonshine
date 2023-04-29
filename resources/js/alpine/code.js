/* Flask */

import CodeFlask from 'codeflask';

export default (id, config={}) => ({
    flaskInstance: null,
    init() {
        const input = document.getElementById(id);
        this.flaskInstance = new CodeFlask(this.$el, config)
        this.flaskInstance.onUpdate((code) => input.value = code)
        this.flaskInstance.updateCode(input.value);
    },
})
