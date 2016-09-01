var git = require('git-promise');

describe("exclude:var", function() {
    it("ensure var not included in commit", function(done) {
        git('ls-files ' + APP_ROOT + '/var').then(function(standardout){
            expect(standardout).toBeFalsy();
            done();
        });
    });
});
