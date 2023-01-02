import {defineComponent} from "vue";

const emitName: string = 'shine:input'

export default defineComponent({
    methods: {
        handleChanges() {
            const emitData = {
                // @ts-ignore
                fieldName: this.field.name,
                // @ts-ignore
                value: this.field.value.value
            }

            // @ts-ignore
            this.$emit(emitName, emitData)
        },

    }
})

export {
    emitName
}
