define(['jquery'], function ($) {
    'use strict';

    return function () {
        $(document).ready(function () {
            console.log('Before ref clear 1');
            window.Scout.clearStorage(window.Scout.storageDB, true);
            console.log('After ref clear 2');
        });
    }
});


