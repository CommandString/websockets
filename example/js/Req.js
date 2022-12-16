export class Req {
    constructor(endpoint) {
        this.data = {}

        this.setData("endpoint", endpoint)
    }

    static new(endpoint) {
        return new this(endpoint)
    }

    setData(name, value) {
        this.data[name] = value

        return this
    }

    getData(name) {
        return this.data[name]
    }

    stringify() {
        return JSON.stringify(this.data)
    }

    setHandler(handler) {
        if (handler instanceof Function) {
            this.handler = handler
        } else {
            console.error("You must supply a function as the handler parameter")
        }
    }

    setErrorHandler(errorHandler) {
        if (errorHandler instanceof Function) {
            this.errorHandler = errorHandler
        } else {
            console.error("You must supply a function as the errorHandler parameter")
        }
    }
}