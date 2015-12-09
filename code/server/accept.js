"use strict";
var url = require('url');
var childProcess = require('child_process');
var redis = require("redis"),
    client = redis.createClient();


function accept(request, response)
{
    response.setHeader('Content-Type', 'application/json');

    var urlParsed = url.parse(request.url, true);
    if (urlParsed.pathname == '/index.php') {
        var data = JSON.stringify(urlParsed.query);

        childProcess.execFile(__dirname+'/../web/registermo', [data], function(err/*, stdout, stderr*/) {
            if (err != null) {
                response.statusCode = 500;
                response.end(JSON.stringify(err));
            }
            response.statusCode = 200;
            var result = {"status": "ok"};
            response.end(JSON.stringify(result));
        });

        /*client.lpush(["request_queue", data], function(){
            response.statusCode = 200;
            var result = {"status": "ok"};
            response.end(JSON.stringify(result));
        });*/
    } else {
        response.statusCode = 404;
        response.end(JSON.stringify(urlParsed.query));
    }
}

module.exports = accept;