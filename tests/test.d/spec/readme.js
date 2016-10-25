var child_process = require('child_process');

describe("Readme", function() {

    var tasksDir = "./test.d/task";

    it("Readme Test", function (done) {

        var exit_code = 0;

        child_process.exec('./readme.sh', {cwd: tasksDir, timeout:60000, env: process.env}, function (error, stdout, stderr) {

            if (error) {
                exit_code = error.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    }, 60000);
});