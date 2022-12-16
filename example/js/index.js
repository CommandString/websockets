import { Req } from "./Req.js"
import { Res } from "./Res.js"
import { Ws } from "./Ws.js"

$.toast({
    "title": "Attempting to connect to websocket...",
    "class": "yellow"
})

let ws = null
let connected = false

function connect() {
    ws = new Ws(new WebSocket("ws://localhost:8080"))

    ws.websocket.onopen = () => {
        $.toast({
            "title": "Connected to websocket",
            "class": "green"
        })

        $("body > .segment").removeClass("loading")

        connected = true
    }

    ws.websocket.onmessage = (event) => {
        let res = new Res(event.data)

        if (typeof res.request.requestId != "undefined") {
            ws.handleRequest(res)
        }
    }

    ws.websocket.onclose = (event) => {
        let delay = 2000

        let title = (connected) ? "Websocket Connection Closed" : "Failed To Connect To Websocket"

        $.toast({
            "title": title,
            "message" : `Attempting to reconnect in ${delay} milliseconds`,
            "class": "red",
            "displayTime": delay
        })

        $("body > .segment").addClass("loading")

        setTimeout(() => {
            connect()
        }, delay)

        connected = false
    }
}

connect()

// USED FOR EXAMPLE PAGE
$("#addField").click(() => {
    let fields = $(`[template='data-fields']`).clone()

    fields.removeAttr("template")
    fields.appendTo("#data-fields")
});

$("#resetFields").click(() => {
    $("#data-fields > .fields").remove()
});

$("#sendRequest").click(() => {
    ws.newRequest($(`[name='endpoint']`).val())

    let key = ""
    $("#data-fields .field input").each(function( i ) {
        if (i % 2 == 0) {
            key = $(this).val()
        } else {
            ws.setData(key, $(this).val())
            key = ""
        }

        if (i == $("#data-fields .field input").length - 1) {
            function syntaxHighlight(json) { // thanks StackedOverflow for this one XD
                if (typeof json != 'string') {
                     json = JSON.stringify(json, undefined, 2);
                }
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    var cls = 'number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'key';
                        } else {
                            cls = 'string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'boolean';
                    } else if (/null/.test(match)) {
                        cls = 'null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                });
            }

            ws.send(res => {
                $("#response pre").html(syntaxHighlight(res))
                $("#responseHeader").removeClass("red").addClass("green").text("Response Successful")
            }, res => {
                $("#response pre").html(syntaxHighlight(res))
                $("#responseHeader").removeClass("green").addClass("red").text("Response Failed")
            })
        }
    })
})