export default () => ({
    dispatchAsyncEvent(data) {
        if(this.$root.classList.contains('btn-primary')) {
            this.$dispatch('async-table', {queryTags: 'query-tag=null'})
            this.$root.classList.remove('btn-primary')
            return
        }

        this.$dispatch('async-table', {queryTags: 'query-tag=' + data})

        document.querySelectorAll('.query-tag-button').forEach(function (element) {
            element.classList.remove("btn-primary")
        })

        this.$root.classList.add('btn-primary')
    }
})