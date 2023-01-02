type PDM = PropertyDescriptorMap

export class ClassSAssign<T> {
    constructor(private objectToSpread: T, private klass: T) {}

    private propertyDescriptorOptions = {
        enumerable: true,
        writable: true,
    }

    public apply(): void {
        const map = this.getPropertiesDescriptorMap()
        Object.defineProperties(this.klass, map)
    }

    private getPropertiesDescriptorMap(): PDM {
        // @ts-ignore
        return Object.entries(this.objectToSpread).reduce(
            (obj: PDM, entry) => this.getPropertyDescriptorMap(obj, entry),
            {}
        )
    }

    private getPropertyDescriptorMap(
        obj: PDM,
        [key, value]: [string, any]
    ): PDM {
        return {
            ...obj,
            [key]: {
                value,
                ...this.propertyDescriptorOptions,
            },
        }
    }
}
