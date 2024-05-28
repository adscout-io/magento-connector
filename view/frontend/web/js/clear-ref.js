define(['jquery'], function ($) {
    'use strict';

    return function () {
        $(document).ready(function () {
            window.Scout.clearStorage(window.Scout.storageDB, true);
        });
    }
});


