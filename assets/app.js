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
import { Popover } from 'bootstrap';
import './styles/bootstrap-nightshade.css';

import { rotateTheme } from './theme-handling.js';
window.rotateTheme = rotateTheme;

$(function() {

    // Bootstrap 5 Popovers
    const popoverTriggerList = [...document.querySelectorAll('[data-bs-toggle="popover"]')];
    console.log(`Reinitializing ${popoverTriggerList.length} popovers`);
    popoverTriggerList.forEach(popoverTriggerEl => {
        new Popover(popoverTriggerEl);
    });

    // Theme button in nav
    rotateTheme(true);
});

import handleScroll from './scroll.js';
window.onscroll = handleScroll;