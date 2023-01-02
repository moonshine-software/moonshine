const modelActionHandlers = {
    methods: {
        /**
         * Delete model
         *
         * @param id Model ID
         */
        modelDeleteHandler(id: string | number): void {
            alert(`Delete ${id}`)
        },

        /**
         * Edit model
         *
         * @param id Model ID
         */
        modelEditHandler(id: string | number): void {
            alert(`Edit ${id}`)
        },

        /**
         * Edit model
         *
         * @param id Model ID
         */
        modelForceDeleteHandler(id: string | number): void {
            alert(`ForceDelete ${id}`)
        }
    }
}

export default modelActionHandlers
