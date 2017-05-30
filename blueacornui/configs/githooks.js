/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

module.exports = {
    dev: {
        options: {
            dest: '../.git/hooks'
        },
        'post-merge': {
            taskNames: 'compile'
        },
        'post-checkout': {
            taskNames: 'compile'
        }
    }
};
