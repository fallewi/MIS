var child_process = require('child_process');

describe("PHP Mess Detector", function() {

    var tasksDir = "./test.d/task";

    it("Run PHP Mess Detector", function (done) {

        var exit_code = 0;

        child_process.exec('./messdetector.sh', {cwd: tasksDir, timeout:60000}, function (error, stdout, stderr) {

            if (error) {
                exit_code = error.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    }, 60000);
});