/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var path = require('./path');

module.exports = {
    cache: {
        command: [
            'cd <%=path.webroot%>',
            'rm -rf var/cache/* var/full_page_cache/*'
        ].join('&&')
    },
    setup: {
        command: [
            'mv <%=path.webroot%>/app/design/frontend/blueacorn/gp <%=path.webroot%>/app/design/frontend/<%=path.defaultPackage%>/<%=path.defaultTheme%>',
            'mv <%=path.webroot%>/skin/frontend/blueacorn/gp <%=path.webroot%>/skin/frontend/<%=path.defaultPackage%>/<%=path.defaultTheme%>',
            'cd <%=path.webroot%>',
            'n98-magerun.phar config:set design/package/name <%=path.defaultPackage%>',
            'n98-magerun.phar config:set design/theme/default <%=path.defaultTheme%>',
            'n98-magerun.phar config:set dev/template/allow_symlink 1'
        ].join('&&')
    }
};
