# commandstring/websockets #
A boilerplate for PHP websockets that connect to javascript

# Setup

`git clone https://github.com/CommandString/websockets`

`composer install`

# Looking at the example #

`php index.php`

*open another terminal*

run `php -S localhost:8000 -t ./` in the example directory

# Creating Endpoints

Add a static method to the Requests class in `./Requests.php`, name the method after the name of the endpoint

```php
public static function test(Message $request, string $name) { // ...
```

The first parameter will be the $request then you can list any parameters after that

In the body of the request you can do whatever you need then respond to the request. The full method would look something like this

```php
public static function test(Message $request, string $name) {
    // ...
    $request->setResponseData("name", $name);
    $request->respond();
}
```

# Message Class

Inside of your endpoint handler you are giving an instance of Message which has three things. The response, the request, and then errors. 

You can get the request by using... `Message::getRequest()` which is an stdClass  

You can add data to the response with `Message::setResponseData(string $key, mixed $value)`

Lastly if you need to add error messages to the response you can use `Message::addError(string $message)`

# Creating a connection with the websocket server

First you need to connect to the websocket, we'll use the example websocket url which is `ws://localhost:8080`

```js
let ws = new Ws(new WebSocket("ws://localhost:8080"))
let connected = false
```

Next you'll need to add handlers for `onMessage`, `onOpen`, `onClosed` and if you want `onError`.
```js
ws.onOpen = () => {
    connected = true
}

ws.onClose = () => {
    connected = false
}

ws.websocket.onmessage = (event) => {
    let res = new Res(event.data)

    if (typeof res.request.requestId != "undefined") {
        ws.handleRequest(res)
    }
}
```

*You can use the connected variable to make sure you're connect to the websocket server before sending a request.*


# Building & Sending A Request

```js
ws.newRequest("endpoint").setData("key", "value").send(res => {
    console.log(res)
}, res => {
    console.error(res)
});
```

We can use the newRequest method to start a new request, then setData to add parameters to our request. When we send the request we can then specify a handler for when a response is returned with no errors, and then an error handler for when there's errors returned

I would recommend looking over the example provided for more insight on how to utilize this boilerplate