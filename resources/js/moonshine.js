export class MoonShine {
    constructor() {
        this.successAsyncFunctions = {}
    }

    successAsyncFor(name, successFunction) {
        if(typeof successFunction === 'function') {
            this.successAsyncFunctions[name] = successFunction
        }
    }
}