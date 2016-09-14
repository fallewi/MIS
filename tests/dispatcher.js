var Jasmine = require('jasmine');
var jasmine = new Jasmine();

global.REPO_ROOT = require("path").resolve(__dirname + '/../');
global.APP_ROOT = REPO_ROOT + '/webroot';

// read configuration from jasmine.json, export to specVars
process.env.ENV_VAR_CONFIG_FILE = 'jasmine.json';
specVars = require('var');

jasmine.loadConfig(specVars);

jasmine.configureDefaultReporter({
    showColors: true
});

jasmine.onComplete(function(passed) {
    if(passed) {
        console.log('All specs have passed');
	process.exit(0);
    }
    else {
        console.log('At least one spec has failed');
	process.exit(1);
    }
});
jasmine.execute();
