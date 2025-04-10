import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import 'bootstrap/dist/css/bootstrap.min.css';
import { Popover } from 'bootstrap';

document.addEventListener('turbo:load', () => {
    // Bootstrap 5 Popovers
    const popoverTriggerList = [...document.querySelectorAll('[data-bs-toggle="popover"]')];
    console.log(`Reinitializing ${popoverTriggerList.length} popovers`);
    popoverTriggerList.forEach(popoverTriggerEl => {
        new Popover(popoverTriggerEl);
    });
});

import handleScroll from './scroll.js';
window.onscroll = handleScroll;

import { themeHandlerInit } from './theme-handling.js';
themeHandlerInit(); // Run only on firt page load