var git = require('git-promise');

describe("exclude:var", function() {
    it("ensure var not included in commit", function() {
        git('ls-files ' + APP_ROOT + '/var').then(function(standardout){
            expect(standardout).toEqual(false);
        });
    });
});