var http = require('http');
var accept = require('./accept');

var server = new http.Server();

server.listen(80, '0.0.0.0');

server.on('request', accept);