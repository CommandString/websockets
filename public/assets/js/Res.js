export class Res {
    constructor(
        jsonString
    ) {
        this.data = JSON.parse(jsonString)

        this.response = this.data.response
        this.errors = this.data.errors
        this.request = this.data.request

        delete this.data
    }

    __set(name, value) {
        return;
    }
}