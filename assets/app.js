import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import $ from 'jquery';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './styles/bootstrap-nightshade.css';
/*
document.addEventListener('turbo:load', function (e) {
    const popoverTriggerList = [...document.querySelectorAll('[data-bs-toggle="popover"]')];
    console.log(`Reinitializing ${popoverTriggerList.length} popovers`);
    popoverTriggerList.forEach(popoverTriggerEl => {
        new bootstrap.Popover(popoverTriggerEl);
    });
});
*/
$(document).ready(function() {
    //$('[data-toggle="popover"]').popover();
    const popoverTriggerList = [...document.querySelectorAll('[data-bs-toggle="popover"]')];
    console.log(`Reinitializing ${popoverTriggerList.length} popovers`);
    popoverTriggerList.forEach(popoverTriggerEl => {
        new bootstrap.Popover(popoverTriggerEl);
    });
});

import './scroll.js';