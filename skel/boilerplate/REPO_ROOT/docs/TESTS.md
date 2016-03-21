tests
=====
#### Example [test.js](../tests/test.d/example.js)

```
describe("A spec", function() {
    it("is just a function, so it can contain any code", function() {
        var foo = 0;
        foo += 1;

        expect(foo).toEqual(1);
    });

    it("can have more than one expectation", function() {
        var foo = 0;
        foo += 1;

        expect(foo).toEqual(2);
        expect(true).toEqual(true);
    });
});
```
###### Global Variables
Two global variables have been provided for all test files
from [dispatcher.js](../tests/dispatcher.js#L4)

```
global.REPO_ROOT;
global.APP_ROOT;
```

REPO_ROOT points to the root of the repo.
APP_ROOT points to the root of the app, generally the `/webroot` directory

[Learn More About Jasmine](http://jasmine.github.io/edge/introduction.html#section-Included_Matchers)