var child_process = require('child_process');

describe("Asynchronous PHP specs", function() {

    var tasksDir = "./test.d/php-tests/tasks";

    it("Run PHPCodeSniffer", function (done) {

        var exit_code = 0;

        child_process.exec('./codesniffer.sh', {cwd: tasksDir}, function (err, stdout, stderr) {
            if (err) {
                exit_code = err.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    });

    it("Run PHPMessDetector", function (done) {

        var exit_code = 0;

        child_process.exec('./messdetector.sh', {cwd: tasksDir}, function (err, stdout, stderr) {
            if (err) {
                exit_code = err.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    });

    it("Run PHPLint", function (done) {

        var exit_code = 0;

        child_process.exec('./lint.sh', {cwd: tasksDir}, function (err, stdout, stderr) {
            if (err) {
                exit_code = err.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    });
});