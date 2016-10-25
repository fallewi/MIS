var child_process = require('child_process');

describe("Template Queries", function() {

    var tasksDir = "./test.d/task";

    it("Tests for query() calls in template files", function (done) {

        var exit_code = 0;

        child_process.exec('./template-queries.sh', {cwd: tasksDir, timeout:60000, env: process.env}, function (error, stdout, stderr) {

            if (error) {
                exit_code = error.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    }, 60000);
});