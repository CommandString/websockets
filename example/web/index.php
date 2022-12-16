<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Websocket Test</title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.0/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.0/dist/semantic.min.js"></script>
    <script type="module" src="js/index.js"></script>

    <style>
        body > .segment {
            height: 103%;
            width: 100%;
            border-radius: unset !important;
            background: #000000 !important;
            overflow: scroll;
        }

        #response {
            height: 20vh;
        }

        body {
            overflow: hidden;
        }

        [template] {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="ui inverted violet loading segment">
        <div class="ui massive inverted dividing header">Websocket Example Page</div>
        <div class="ui inverted form">
            <div class="eight wide field">
                <label>Endpoint</label>
                <input type="text" name="endpoint">
            </div>
            <div class="ui inverted dividing header">Data</div>

            <div id="data-fields"></div>

            <div class="ui divider"></div>
            <div id="sendRequest" class="ui inverted green button">
                Send Request
            </div>
            <div id="addField" class="ui inverted yellow button">
                Add Data Field
            </div>
            <div id="resetDataFields" class="ui inverted red button">Reset Data Fields</div>
            <div class="ui massive inverted dividing header">Response</div>
            
            <div class="ui inverted segment" id="response"></div>
        </div>
    </div>

    <!-- DATA FIELD TEMPLATE --> 
    <div template="data-fields" class="fields">
        <div class="four wide field">
            <label>Key</label>
            <input type="text" name="key">
        </div>
        <div class="four wide field">
            <label>Value</label>
            <input type="text" name="value">
        </div>
    </div>
</body>
</html>