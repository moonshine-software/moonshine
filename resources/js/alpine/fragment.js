export default (asyncUpdateRoute = '') => ({
    asyncUpdateRoute: asyncUpdateRoute,

    fragmentUpdate() {
        if(this.asyncUpdateRoute === '') {
            return;
        }

        axios
            .get(this.asyncUpdateRoute)
            .then(response => {
                this.$root.outerHTML = response.data
            })
            .catch(error => {
                //
            })
    },
})