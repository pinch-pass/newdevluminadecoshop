{*
* Copyright (C) 2017-2018 Petr Hucik <petr@getdatakick.com>
*
* NOTICE OF LICENSE
*
* Licensed under the DataKick Regular License version 1.0
* For more information see LICENSE.txt file
*
* @author    Petr Hucik <petr@getdatakick.com>
* @copyright 2017-2019 Petr Hucik
* @license   Licensed under the DataKick Regular License version 1.0
*}
<div id="conseqs-app">
    Please wait...
</div>
<script>
    (function () {
        var started = false;
        var attempt = 0;

        function startConseqsApp() {
            if (started) {
                return;
            }
            if (window.startConseqs) {
                started = true;
                startConseqs({$conseqs|json_encode});
            } else {
                attempt++;
                console.log('[' + attempt + '] startConseqs not loaded yet, waiting...');
                setTimeout(startConseqsApp, 500);
            }
        }

        startConseqsApp();
    })();
</script>
