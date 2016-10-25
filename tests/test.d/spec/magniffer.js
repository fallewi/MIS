var child_process = require('child_process');

describe("Magniffer", function() {

    var tasksDir = "./test.d/task";

    it("Run Magniffer", function (done) {

        var exit_code = 0;

        child_process.exec('./magniffer.sh', {cwd: tasksDir, timeout:60000, env: process.env}, function (error, stdout, stderr) {

            if (error) {
                exit_code = error.code;
            }
            expect(exit_code).toEqual(0, stdout);
            done();
        });
    }, 60000);
});