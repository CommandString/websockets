import { Req } from "./Req.js";
import { Res } from "./Res.js";

export class Ws {
    constructor(
        websocket
    ) {
        if (!websocket instanceof WebSocket) {
            console.error("You must supply an instance of WebSocket")
            return;
        }

        this.websocket = websocket
        this.req = null
        this.outgoingRequests = []
    }

    send(handler = () => {}, errorHandler = () => {}) {
        if (this.req !== null) {
            let requestId = this.outgoingRequests.length
            this.setData("requestId", requestId)
            this.setHandler(handler)
            this.setErrorHandler(errorHandler)
            this.outgoingRequests[requestId] = this.req
            this.websocket.send(this.req.stringify())
            this.req = null
        } else {
            console.error("A request has not been created yet!")
        }
    }

    handleRequest(res) {
        if (res instanceof Res) {
            let req = this.outgoingRequests[res.request.requestId];
            
            this.outgoingRequests.splice(this.outgoingRequests.indexOf(res.request.requestId), 1);
            
            if (res.errors.length > 0) {
                if (typeof req.errorHandler != "undefined") {
                    req.errorHandler(res)
                }
            } else {
                if (typeof req.handler != "undefined") {
                    req.handler(res)
                }
            }
        } else {
            console.error("An instance of Res must be supplied as the res parameter");
        }
    }

    newRequest(endpoint) {
        this.req = new Req(endpoint);

        return this;
    }

    setData(key, value) {
        if (this.req == null) {
            console.error("A request has not been created yet!")
            return
        }

        this.req.setData(key, value)
        
        return this
    }

    setHandler(handler) {
        if (this.req == null) {
            console.error("A request has not been created yet!")
            return
        }

        this.req.setHandler(handler)

        return this
    }

    setErrorHandler(errorHandler) {
        if (this.req == null) {
            console.error("A request has not been created yet!")
            return
        }

        this.req.setErrorHandler(errorHandler)

        return this
    }
}