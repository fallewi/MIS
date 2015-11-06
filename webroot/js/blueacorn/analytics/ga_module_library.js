(function($) {

    /**
      * @package     N/A
      * @version     1.0
      * @author      Blue Acorn <code@blueacorn.com>, Sean Emmel <sean.emmel@blueacorn.com>
      * @copyright   Copyright © 2015 Blue Acorn.


     * Protect window.console method calls, e.g. console is not defined on older 
     * versions of IE unless dev tools are open, and IE doesn't define console.debug
     * 
     * Chrome 41.0.2272.118: debug,error,info,log,warn,dir,dirxml,table,trace,assert,count,markTimeline,profile,profileEnd,time,timeEnd,timeStamp,timeline,timelineEnd,group,groupCollapsed,groupEnd,clear
     * Firefox 37.0.1: log,info,warn,error,exception,debug,table,trace,dir,group,groupCollapsed,groupEnd,time,timeEnd,profile,profileEnd,assert,count
     * Internet Explorer 11: select,log,info,warn,error,debug,assert,time,timeEnd,timeStamp,group,groupCollapsed,groupEnd,trace,clear,dir,dirxml,count,countReset,cd
     * Safari 6.2.4: debug,error,log,info,warn,clear,dir,dirxml,table,trace,assert,count,profile,profileEnd,time,timeEnd,timeStamp,group,groupCollapsed,groupEnd
     * Opera 28.0.1750.48: debug,error,info,log,warn,dir,dirxml,table,trace,assert,count,markTimeline,profile,profileEnd,time,timeEnd,timeStamp,timeline,timelineEnd,group,groupCollapsed,groupEnd,clear
     */
    (function() {
      // Union of Chrome, Firefox, IE, Opera, and Safari console methods
      var methods = ["assert", "assert", "cd", "clear", "count", "countReset",
        "debug", "dir", "dirxml", "dirxml", "dirxml", "error", "error", "exception",
        "group", "group", "groupCollapsed", "groupCollapsed", "groupEnd", "info",
        "info", "log", "log", "markTimeline", "profile", "profileEnd", "profileEnd",
        "select", "table", "table", "time", "time", "timeEnd", "timeEnd", "timeEnd",
        "timeEnd", "timeEnd", "timeStamp", "timeline", "timelineEnd", "trace",
        "trace", "trace", "trace", "trace", "warn"];
      var length = methods.length;
      var console = (window.console = window.console || {});
      var method;
      var noop = function() {};
      while (length--) {
        method = methods[length];
        // define undefined methods as noops to prevent errors
        if (!console[method])
          console[method] = noop;
      }
    })();
    

    /**
     * The Constructor definition for `GaqTracker` which gets attached to the global window object.
     *
     * When instantiating this constructor, you must pass it an array of object literals. Each object
     * literal should have the following structure (which can be built via the corresponding Node script):

        var sampleobj = {
            trackType: '', //=> 'event', 'pageView', or 'pageViewOnLoad'
            el: null, //=> null by default, this is the element CSS selector as a STRING (ex: 'ul.main-nav > li a') and MUST be supplied if trackType is 'event'
            page: '', //=> the string representing the page. Examples include: 'Sitewide', 'Homepage', 'Category', 'Product', 'Cart', 'Checkout', etc.
            type: '', //=> a custom description of the event (i.e. a GA "Event Action")
            label: null, //=> leave null for current URL (window.location.href). Otherwise, supply the custom string indicated in the tag plan
            eventType: 'click', //=> 'click' by default, can use any event listener here
            bodyClass: null, //=> null by default, otherwise, specify a <body> class as a STRING  ** required if trackType is pageViewOnLoad!! **
            row: null //=> null by default, used for debugging/error reporting, this will be the corresponding row in the CSV file for the object
        };

     * So one could instantiate this constructor with only one object, but it would still need to be in an array! For example:

        // Instantiate constructor and call from instantiation
        var BA_GAQ = new GaqTracker([sampleobj]);

    * The main use will be to instantiate the constructor with an array of many objects. I suggest that you simply copy/paste
    * the data returned from the Node script and save it as a variable. Then instantiate the constructor and pass in this variable.
    * For example, here the variable `data` gets set equal to the  JSON data copied/pasted from the Node script. You can see that
    * it is simply an array of object literals:

        // Set the array of objects equal to a variable
        var data =  [ 
                        { 
                            trackType: 'event',
                            el: 'ul li.some_class',
                            page: 'Homepage',
                            type: 'a custom description of the event/action (a GA event "Action")',
                            label: null,
                            eventType: 'click',
                            bodyClass: 'cms-index-index',
                            row: 1 
                        },
                        { 
                            trackType: 'event',
                            el: 'body #nav .some_element',
                            page: 'Category',
                            type: 'another custom description of the event/action (a GA event "Action")',
                            label: null,
                            eventType: 'click',
                            bodyClass: 'catalog-category-view',
                            row: 2 
                        },
                        { 
                            trackType: 'pageView',
                            el: null,
                            page: 'Category',
                            type: 'yet another custom description of the event/action (a GA event "Action")',
                            label: null,
                            eventType: '',
                            bodyClass: 'catalog-category-view',
                            row: 3 
                        } 
                    ];

        // Now instantiate the constructor and pass in the array of objects
        var BA_GAQ = new GaqTracker(data); 

        // Then run the main tracking method to engage tracking
        BA_GAQ.trackAll();

     *
     * @param {Array} objects - an array of object literals representing each event/pageview to track
     *
    **/
    window.GaqTracker = function(objects) {
        if (objects instanceof Array === false) {
            console.error('You must supply an ARRAY of objects!');
            return false;
        }
        this.mode = 'universal';
        this.dataSet = objects || [];
    };

    // Functionality attached to prototype
    GaqTracker.prototype = {

        /**
         * Change the mode between "classic" and "universal" to engage proper functionality.
         * Default mode is now "universal"
         *
         * @param {String} mode - "classic" or "universal"
         *
         */
        setMode: function(mode) {
            if (mode.toLowerCase() !== 'classic' && mode.toLowerCase() !== 'universal') {
                console.error('Invalid mode specified! Please choose "classic" or "universal"');
            } else {
                this.mode = mode.toLowerCase();
            }
        },

        /**
         * Check the current mode of the module: "classic" (ga.js) or "universal" (analytics.js)
         *
         * @return {String} - the current mode specified, either "classic" or "universal"
         *
         */
        getMode: function() {
            return this.mode;
        },

        /**
         * Validates that various properties of the object (config) contain
         * valid values. See more notes within method.
         *
         * @param {Object Literal} config - the main object build for any given tracking
         * 
         */
        propertyCheck: function(config) {
            /* 
            Properties that are checked are: 
                * trackType --> should be 'event' or 'pageView' or 'pageViewOnLoad'
                * el --> if trackType is set to 'event' then an element CSS selector MUST be supplied as a string!
                * bodyClass --> defaults to null; if trackType is set to 'pageViewOnLoad' then you MUST have a bodyClass specified
                * row --> this is set via the Node script and indicates the corresponding row in the spreadsheet for the `config` object at hand.
                          If objects are manually added later and this param isn't supplied or is undefined, it will be set to null
            Properties that are NOT checked are:
                * page --> a custom string indicating page being tracked
                * type --> a custom string that varies (usually a description of the event, i.e. GA "Event Action")
                * label --> can be custom string; defaults to null, and if it stays this way, gets converted to current URL
                * eventType --> not practical to check for ALL possible event types, and you could use custom ones anyway
            */

            // trackType
            if (config.trackType !== 'event' && config.trackType !== 'pageView' && config.trackType !== 'pageViewOnLoad') {
                console.error('Error! Row ' + config.row + '. Wrong trackType specified! Please use "event" or "pageView" or "pageViewOnLoad" (check your casing!)');
                return;
            }
            // el 
            if (config.trackType === 'event') {
                if (!config.el) {
                    console.error('Error! Row ' + config.row + '. No element specified, but "trackType" set to "event"! You must supply a valid CSS selector as the `el` property! (That or the element is null and you need to modify your selector.)');
                    return;
                } else if (typeof config.el !== 'string') {
                    console.error('Error! Row ' + config.row + '. You must supply a CSS selector as a STRING! Make sure you\'re not attempting to use a jQuery object!');
                }
            }
            // bodyClass
            if (config.trackType === 'pageViewOnLoad' && config.bodyClass === null) {
                console.error('Error! Row ' + config.row + '. No body class specified! Virtual pageview tracking requires a body class!');
                return;
            }
            // row: set to null if undefined or nonexistent 
            if (!config.row || config.row === undefined) {
                config.row = null;
            }
        },

        /**
         * Transforms the `trackType` property of the `config` object to a compatible GA format
         * based on whether the GaqTracker mode is set to "classic" or "universal"
         *
         * @param {String} trackType - 'event', 'pageView', or 'pageViewOnLoad'
         * @return {String} - the transformed trackType 
         *
         */
        transformTrackType: function(trackType) {
            trackType = trackType.toLowerCase();

            if (this.mode === 'classic') {
                if (trackType === 'event') {
                    trackType = '_trackEvent';
                } else if (trackType === 'pageview' || trackType === 'pageviewonload') {
                    trackType = '_trackPageview';
                }
            } else if (this.mode === 'universal') {
                if (trackType === 'pageview' || trackType === 'pageviewonload') {
                    trackType = 'pageview';
                }
            }

            return trackType;
        },

        /**
         * Actually pushes the tracking info to GA. If the mode is set to "classic" then
         * this function will send tracking using the `_gaq.push()` method. See here for
         * more info: <<  https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApi_gaq#_gaq.push  >>
         *
         * If the mode is set to "universal" then this method will send tracking using 
         * the `ga()` method. See here for more info: <<  https://developers.google.com/analytics/devguides/collection/analyticsjs/events  >>
         *
         * @param {String} trackType - the type of tracking; currently 'event', 'pageView', and 'pageViewOnLoad' are the only types supported
         * @param {String} page - the name of the page tracking fires on (see sample object for list of possible values)
         * @param {String} type - a custom string, the GA "Event Action," which describes the event
         * @param {String} label - custom/unique text, oftentimes the current URL
         *
         */
        trackingHelper: function(trackType, page, type, label) {
            trackType = this.transformTrackType(trackType); // coerce into proper GA format based on "classic" or "universal" mode

            if (this.mode === 'classic') { // GA "Classic"
                if (typeof window._gaq !== undefined  && window._gaq) {
                    // jQuery grep filters out any parts of the array that are undefined
                    _gaq.push($.grep([trackType, page, type, label], function(arg) {
                        return(arg);
                    }));
                } else {
                    console.error('Error! Mode set to "classic" but window._gaq is undefined!');
                }
            } else if (this.mode === 'universal') { // GA "Universal Analytics"
                if (typeof window.ga !== undefined && window.ga) {
                    if (trackType === 'pageview') {
                        ga('send', trackType, page); // virtual pageview tracking
                    } else {
                        ga('send', trackType, page, type, label); // event tracking, passing null values is okay
                    }
                } else {
                    console.error('Error! Mode set to "universal" but window.ga is undefined!');
                }
            }
        },
      
        /**
         * The main tracking method that handles the logic for tracking events, pageviews, or
         * virtual pageviews on load (e.g., 'event', 'pageView', and 'pageViewOnLoad' respectively) 
         * based on the trackType for the `config` object. It utilizes the following properties from
         * the `config` object:
         *
         * config.trackType --  determines what type of tracking occurs (e.g., event, pageview, or virtual pageview tracking)
         * config.label     --  the custom label as defined in the spreadsheet. If null, will default to current URL
         * config.el        --  the CSS selector (as a string), which is what the 'eventType' is bound to, i.e. the
         *                      actual DOM element
         * config.eventType --  the event listener to bind to `config.el` (the DOM element)
         * config.type      --  a custom description of the event (i.e. a GA "Event Action")
         * config.page      --  the string representing the page (e.g., 'Sitewide', 'Homepage', etc.)
         * config.bodyClass --  the class on the <body> element to look for when tracking Virtual Pageviews
         *
         * That being said, this method accepts two parameters; the main object literal for any
         * given tracking, and an optional callback to run when the tracking fires for the currrent 
         * object (supplied via the `trackAll` method)
         *
         * @param {Object Literal} config - the main object build for any given tracking
         * @param {Function} callback - a callback to be executed if supplied (passed from `trackAll` method)
         *
         */
        autoTracker: function(config, callback) {
            var self = this;

            // If no custom label being used, then use current URL
            if (config.label === null) {
                config.label = window.location.href;
            }
            if (config.el !== null && config.el !== 'undefined') {
                if (config.trackType.toLowerCase() === 'event' || config.trackType.toLowerCase() === 'pageview') { 
                    if ($(config.el).length > 0) {
                        // Element exists, bind event directly to element
                        $(config.el).on(config.eventType, function(e) {
                            self.trackingHelper(config.trackType, config.page, config.type, config.label);
                        });
                    } else {
                        // Element not on page yet, use delegated event bound to $(document)
                        $(document).on(config.eventType, config.el, function(e) {
                            self.trackingHelper(config.trackType, config.page, config.type, config.label);
                        });
                    }
                    if (callback && typeof callback === 'function') {
                        callback.apply(config); //=> keep reference to `config` object in callback 
                    }
                }
            } 
            if (config.trackType.toLowerCase() === 'pageviewonload') {
                self.trackPageViewOnLoad(config.bodyClass, config.page);
                if (callback && typeof callback === 'function') {
                    callback.apply(config); //=> keep reference to `config` object in callback 
                }
            }
        },

        /**
         * This function first validates the properties for each object
         * using the `propertyCheck` method to catch any errors. Then 
         * the `autoTracker` function is executed This is performed
         * for each object in the array of objects.
         *
         * @param {Function} callback - optional callback to be run after tracking fires for each object.
         *                              If present, this callback gets passed to the `autoTracker` method as its 
         *                              second argument.
         *
         */
        trackAll: function(callback) {
            for (var i = 0, l = this.dataSet.length; i < l; i++) {
                var current_object = this.dataSet[i];
                // Validate properties first (check for errors)
                this.propertyCheck(current_object);
                // Then run the tracking
                if (callback && typeof callback === 'function') {
                    this.autoTracker(current_object, callback);
                } else {
                    this.autoTracker(current_object);
                } 
            }
        },



        /* ========================================================================= *\
         *             UTILITY FUNCTIONS: Extensibility for Developers               *
        \* ========================================================================= */


        /**
         * Tracks a Virtual Page View on page load by observing a specified class on the <body> element
         *
         * @param {String} bodyClass - the class name to check for on the <body> element
         * @param {String} page - the name of the page
         *
         */
        trackPageViewOnLoad: function(bodyClass, page) {
            if ($('body').hasClass(bodyClass)) {
                this.trackingHelper('pageViewOnLoad', page, null, null);
            }
        },

        /**
         * Tracks a Virtual Page View and sends the name of the virtual page to GA.
         *
         * @param {String} page - the name of the "virtual" page tracking occured on
         *
         */        
        trackVirtualPageView: function(page) {
            this.manualTracker('pageView', null, page, null, null, null);          
        },

        /**
         * Tracks a Virtual Page View when a specified event occurs on an element and
         * sends the name of the virtual page to GA. 
         * 
         *
         * @param {String} event - the name of the event listener to bind to the element
         * @param {String}||{jQuery Object} element - the element that `event` is bound to, this
                                                      can be either a CSS selector OR a jQuery object
         * @param {String} page - the name of the page as a string
         *
         */  
        trackVirtualPageViewOnEvent: function(event, element, page) {
            var $element = $(element); // coerce into jQuery object in case CSS selector provided

            if ($element.length === 0) {
                console.error('Error! The element does not appear to have been on the page when this function ran! (Its length was 0.)');
                return;
            }

            this.manualTracker('pageView', $element, page, null, null, event);
        },

        /**
         * Sends a GA tracking call of type 'event' with custom information.
         * 
         * @param {String} page - the name of the page tracking fires on
         * @param {String} type - a custom string, the GA "Event Action," which describes the event
         * @param {String} label - custom/unique text, oftentimes the current URL
         *
         */ 
        trackEvent: function(page, type, label) {
            this.trackingHelper('event', page, type, label);
        },

        /**
         * This method is essentially the same as the `autoTracker` method except it allows you 
         * to explicitly define your arguments without configuring them as an object. This can 
         * be benificial when you need to use jQuery selectors for other tracking parameters or
         * if you need to programmtically fire tracking. For example:

            var BA_GAQ = new GaqTracker(data); //=> `data` is the array of objects required for instantiation

            // Programmatically fire tracking for multiple nav elements using jQuery selectors/functions
            $('.some_nav_elements a').each(function() {
                var $this = $(this); //=> set proper reference to $(this)
                BA_GAQ.manualTracker('event', $this, 'Sitewide', $this.text(), window.location.href, 'click');
            });

         * In this example, the `element` argument can be a jQuery object OR a jQuery selector.
         * Also, since this method accepts an event, you would usually use this argument to fire tracking
         * when said event occurs (like in the above example, where this method gets fired on 'click' events
         * attached to the $this element). However, you CAN pass in a value of `null` (the literal null, not
         * a string version) in which case the `trackingHelper` method will be invoked immediately without
         * being bound to an event. This is helpful if you wish to use a delegated event. For example:

            var BA_GAQ = new GaqTracker(data); //=> `data` is the array of objects required for instantiation

            // Bind the 'mousedown' event to the document as a delegated event to target elements
            // that don't currently exist on the page yet
            $(document).on('mousedown', '.some_ajaxed_elements_that_are_not_on_page_yet', function() {
                var $this = $(this); //=> set proper reference to $(this)
                BA_GAQ.manualTracker('event', $this, 'Cart', $this.text(), window.location.href, null);
            });

         * In the example above, the `manualTracker` method gets called whenever the AJAX'ed elements
         * invoke the 'mousedown' event handler. Since we're essentially trying to fire tracking on these
         * elements when you mousedown on them, we don't need to pass in ANOTHER event to the `manualTracker`
         * function, as this would then be a mousedown event inside of a mousedown event. In other words,
         * we would NOT want to do this:

            // DO NOT DO IT LIKE THIS! DO NOT PUT THE SAME EVENT LISTENER INSIDE OF ITSELF
            $(document).on('mousedown', '.some_ajaxed_elements_that_are_not_on_page_yet' function() {
                var $this = $(this);
                BA_GAQ.manualTracker('event', $this, 'Cart', $this.text(), window.location.href, 'mousedown');
            });

         * If you were to program tracking like the above, then you would have to mousedown on the AJAX'ed
         * elements TWICE, since you're binding the tracking to mousedown inside of the `manualTracker` method
         * but binding the `manualTracker` method invocation to a mousedown event bound to the document.
         *
         * 
         * Note that given the structure of this GaqTracker object, you should cache a proper reference
         * to `this` or `$(this)` to pass into the `manualTracker` method.
         *
         * Should you wish to manually fire a pageview on load, then use the `trackPageViewOnLoad` method
         * defined above. If you attempt to track a pageview on load with this `manualTracker` method, an 
         * error will be thrown.
         *
         * @param {String} trackType - determines what type of tracking occurs (e.g., event or virtual pageview tracking)
         * @param {String} || {jQuery Object} element - the element that the 'eventType' is bound to, either as a CSS selector
         *                                              or as a jQuery object. This gets coerced regardless into a jQuery object.
         * @param {String} page - a custom string, usually representing the page (e.g., 'Sitewide', 'Homepage', 'Category', etc.)
         * @param {String} type - a custom description of the event (i.e. a GA "Event Action")
         * @param {String} label - the custom label as defined in the spreadsheet. If null, will default to current URL
         * @param {String} event - the event to bind to the  element. If `null` is provided, then `trackingHelper` will be invoked
         *                         to make a call to Google Analytics without waiting for an event.
         *
         */
        manualTracker: function(trackType, element, page, type, label, event) {
            var self = this,
                trackTypeLower = trackType.toLowerCase(),
                $element = $(element); /* coerce CSS selector into jQuery object if jQuery object not provided */

            if (trackTypeLower === 'pageviewonload') {
                console.error('Error! Please use the `trackPageViewOnLoad` method to track pageviews on load!');
                return;
            } else if (trackTypeLower === 'event' || trackTypeLower === 'pageview') {
                if (label === null) label = window.location.href;
                if ($element) {
                    if (event !== null) {
                        event = event || 'click';
                        $element.on(event, function() {
                            self.trackingHelper(trackType, page, type, label);
                        });
                    } else if (event === null) {
                        self.trackingHelper(trackType, page, type, label);
                    }
                } else if ($element === null) {
                    if (trackTypeLower === 'pageview') {
                        self.trackingHelper(trackType, page, type, label);
                    } else {
                        console.error('Error! You passed in "null" as the `element` argument but the `trackType` is not pageview!');
                        return;
                    }
                }
            } else {
                console.error('Error! Not a valid track type! Please specify "event" or "pageView"');
                return;
            }  
        },

        /**
         * A method to be used for debugging. This method will put a 5px solid outline around any number
         * of elements in the current `dataSet` of your GaqTracker instantiation. The default color of
         * the outline is red, but you may override this by providing an optional second argument representing
         * the color you wish to use instead.
         *
         * Keep in mind that the dataSet is generated via the Node script and CSV, so THIS WILL ONLY WORK 
         * FOR ELEMENTS IN YOUR CSV FILE!!!!
         *
         * There are 3 different values you can pass in as the first argument: 
         *      1) The string "all" - which will outline all appplicable elements in the dataSet
         *      2) An integer corresponding to the `row` number of any given object
         *      3) An array of integers corresponding to the `row` number of any given object
         *
         * Keep in mind that this will only outline *applicable objects* that actually have a defined `el` 
         * property in the CSV and whose element is *currently on the page*. 
         *
         * Sample Usage:
         * -------------
         
            // Create an instance of the constructor
            var BA_GAQ = new GaqTracker(data); //=> `data` is the array of objects required for instantiation
         
            // Add an outline around all elements in the dataSet (i.e. 'data' argument above)
            BA_GAQ.outlineElements('all');

            // Add an outline around the element in the 5th row of the CSV
            BA_GAQ.outlineElements(5);

            // Add an outline around the 5th, 8th, 10th, and 14th elements in the CSV (rows 5, 8, 10, and 14)
            BA_GAQ.outlineElements([5, 8, 10, 14]);

            // Add a blue outline around the elements in the 3rd and 9th rows in the CSV
            BA_GAQ.outlineElements([3, 9], 'blue');

         *
         *
         * @param {String}||{Integer}||{Array}  idx - a string of 'all' for all elements, a single integer
         *                                            corresponding to the row number of the element in the CSV
                                                      file, or an array of integers corresponding to the 
                                                      row numbers for each element in the CSV to outline. 
         * @param {String} color - an optional argument representing the color to set the outline to if
                                   you would like to override the default 'red' color. Any valid CSS color
                                   will work here, including hex, RGB, RGBA, and standard color name.
         *
         */
        outlineElements: function(idx, color) {
            color = color || 'red';
            var error_message = ('Error! Please pass in either an INTEGER corresponding to the "row" property of an object, an ARRAY containing only INTEGERS corresponding to the "row" property of the objects you would like to outline, or the STRING "all" to outline all applicable elements for every object int he dataset.');
            
            // Handle "all" as the argument -- highlight all applicable elements in the dataSet
            if (typeof idx === 'string') {
                if (idx.toLowerCase() === 'all') {
                    for (var i = 0, l = this.dataSet.length; i < l; i++) {
                        var current_object = this.dataSet[i];
                        if (current_object.el !== null && current_object.el !== 'undefined' && $(current_object.el).length > 0) {
                            $(current_object.el).css('outline', '5px solid ' + color);
                        }
                    }
                } else {
                    console.error(error_message);
                    return;
                }
            } else if (typeof idx === 'number') {
                // Handle a single number as the argument -- highlight the element if applicable
                var current_index = parseInt(idx, 10);
                if (isNaN(current_index)) {
                    console.error(error_message);
                    return;
                } else {
                    var current_object = this.dataSet[current_index - 2];
                    if (current_object.el !== null && current_object.el !== 'undefined' && $(current_object.el).length > 0) {
                        $(current_object.el).css('outline', '5px solid ' + color);
                    }
                }
            } else if (idx instanceof Array) {
                // Handle an array of integers as the argument -- highlight all applicable elements in the array
                for (var i = 0, l = idx.length; i < l; i++) {
                    var current_index = parseInt(idx[i] - 2, 10);
                    if (isNaN(current_index)) {
                        console.error(error_message);
                        return;
                    } else {
                        var current_object = this.dataSet[current_index];
                        if (current_object.el !== null && current_object.el !== 'undefined' && $(current_object.el).length > 0) {
                            $(current_object.el).css('outline', '5px solid ' + color);
                        }
                    }
                }
            } else {
                console.error(error_message);
                return;
            }
        },

        /**
         * Adds a method to the GaqTracker prototype, which will be inherited by your instance of
         * the GaqTracker constructor. Note that this method is attached to the *prototype of the
         * GaqTracker instance*. It is NOT attached to the *prototype of your instantiated constructor*!
         *
         * @param {String} method_name - the name of the method to attach to the prototype
         * @param {Function} method - the actual method to execute
         *
         * Sample Usage:
         * -------------
         * You can either create a function expression first and pass the reference into this 
         * method, or you can pass in an anonymous function to be executed. See below.
         *
         * 1) Using a predefined function via function expression:
        
                // Your new method
                var newMethod = function(args) {
                    console.log(args);
                };

                // Create an instance of the constructor
                var BA_GAQ = new GaqTracker(data); //=> `data` is the array of objects required for instantiation

                // Add your method to it
                BA_GAQ.addMethod('newMethod', newMethod);

         * 2) Passing in an anonymous function

                // Create an instance of the constructor
                var BA_GAQ = new GaqTracker(data); //=> `data` is the array of objects required for instantiation

                // Add your method: give it a name, then pass in the anonymous function
                BA_GAQ.addMethod('newMethod', function() {
                    console.log('New method added!');
                });

         * Remember, this method will get attached to the GaqTracker prototype, NOT to your instantiated 
         * constructor's prototype! In other words:

                console.log(BA_GAQ.prototype.newMethod);  //=> undefined  (method is NOT attached to BA_GAQ prototype)
                console.log(GaqTracker.prototype.newMethod); //=> the method just added  (method attached to GaqTracker prototype)
                console.log(BA_GAQ.newMethod); //=> the method just added, inherited from the GaqTracker prototype

         *
         * @param {String} method_name - the name of your new method
         * @param {Function} method - the actual method itself
         *
         */
        addMethod: function(method_name, method) {
            if (typeof method === 'function') {
                GaqTracker.prototype[method_name] = method;
            } else {
                console.error('Error! Not a valid function/method!');
                return;
            }
        }


    }; // end GaqTracker.prototype definition



    /* ======================================================================= *\
     *                  UTILITY FUNCTIONS: String Formatting                   *
    \* ======================================================================= */
    
    /**
     * Capitalize the first letter of a string. Attached to the String prototype.
     *
     * @return {String} - the formatted string
     *
     */
    String.prototype.capitalizeString = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    /**
     * Remove spaces and camelCase a string. Attached to the String prototype.
     *
     * Sample usage:

        "this string gets camel cased".camelCaseString();

        >> "thisStringGetsCamelCased"

     *
     * @return {String} - the formatted string
     *
     */
    String.prototype.camelCaseString = function() {
        return this.replace(/^([A-Z])|\s(\w)/g, function(match, p1, p2, offset) {
            if (p2) return p2.toUpperCase();
            return p1.toLowerCase();        
        });
    };

}(jQuery));
