var child_process = require('child_process');

describe("PHPCodeSniffer", function() {

    var tasksDir = "./test.d/task";

    it("Run PHPCodeSniffer", function (done) {

        var exit_code = 0;

        child_process.exec('./codesniffer.sh', {cwd: tasksDir, timeout:120000}, function (error, stdout, stderr) {

            if (error) {
                exit_code = error.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    }, 120000);
});