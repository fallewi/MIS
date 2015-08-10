/**
* @package     Blueacorn\BlueAcornDevelopmentCore
* @version     1.0
* @author      Blue Acorn <code@blueacorn.com>
* @copyright   Copyright © 2015 Blue Acorn.
*/

var BlueAcornDevelopmentCore = {
    entityMap: {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
    },
    escapeHtmlString: function(string) {
        var self = this;
        return String(string).replace(/[&<>"'\/]/g, function (s) {
            return ba.entityMap[s];
        });
    }
};

/**
 * Extend the Core Object with Development Specific Helper Methods & Variables
 */

ba = jQuery.extend(ba, BlueAcornDevelopmentCore);