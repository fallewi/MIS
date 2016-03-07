var Jasmine = require('jasmine');
var jasmine = new Jasmine();

global.REPO_ROOT = require("path").resolve(__dirname + '/../');
global.APP_ROOT = REPO_ROOT + '/webroot';

jasmine.loadConfigFile('jasmine.json');
jasmine.configureDefaultReporter({
    showColors: true
});

jasmine.onComplete(function(passed) {
    if(passed) {
        console.log('All specs have passed');
    }
    else {
        console.log('At least one spec has failed');
    }
});
jasmine.execute();