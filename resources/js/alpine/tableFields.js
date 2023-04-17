export default (items = {}, emptyData = {}) => ({
    items: items,
    emptyData: emptyData,
    key(item, index) {
        if (item.hasOwnProperty('id')) {
            return item.id + '_' + index;
        }

        if (Object.values(item)[0]) {
            return Object.values(item)[0] + '_' + index;
        }

        return index;
    },
    add() {
        if (Array.isArray(this.items)) {
            this.items.push(this.emptyData);
        } else {
            this.items = [this.emptyData];
        }

        this.$nextTick(() => {
            let newRow = this.$root.querySelector('[data-id]:last-child');
            if (newRow !== null) {
                let removeable = newRow.querySelectorAll('.x-removeable');

                if (removeable !== null) {
                    for (const element of removeable) {
                        element.remove();
                    }
                }
            }
        });

    },
    remove(index) {
        this.items.splice(index, 1);
    }
})
