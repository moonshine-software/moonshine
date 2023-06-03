export default (items = {}) => ({
    items: items,
    key(item, index) {
        if (item.hasOwnProperty('id')) {
            return item.id + '_' + index
        }

        if (Object.values(item)[0] &&
            (/number|string/.test(typeof Object.values(item)[0]))) {
            return Object.values(item)[0] + '_' + index
        }

        return index
    },
    add() {
        let empty = JSON.parse(this.$root.dataset.empty ?? '{}')

        if (Array.isArray(this.items)) {
            this.items.push(empty)
        } else {
            this.items = [empty]
        }

        this.$nextTick(() => {
            let newRow = this.$root.querySelector('[data-id]:last-child')
            if (newRow !== null) {
                let removeable = newRow.querySelectorAll('.x-removeable')

                if (removeable !== null) {
                    for (const element of removeable) {
                        element.remove()
                    }
                }
            }
        })

    },
    remove(index) {
        this.items.splice(index, 1)
    },
})
