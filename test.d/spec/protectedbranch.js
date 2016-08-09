var child_process = require('child_process');

describe("Protected Branch", function() {

    var tasksDir = "./test.d/task";

    it("Checks if the PR is going into a protected branch", function (done) {

        var exit_code = 0;

        child_process.exec('./protectedbranch.sh', {cwd: tasksDir, timeout:60000}, function (error, stdout, stderr) {

            if (error) {
                exit_code = error.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    }, 60000);
});