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

import { themeHandlerInit } from './theme-handling.js';
themeHandlerInit(); // Run only on first page load

document.addEventListener('turbo:visit', () => {
    // fade out the old body
    document.body.classList.add('turbo-loading');
});
document.addEventListener('turbo:before-render', (event) => {
    // when we are *about* to render, start us faded out
    event.detail.newBody.classList.add('turbo-loading');
});
document.addEventListener('turbo:render', () => {
    // after rendering, we first allow the turbo-loading class to set the low opacity
    // THEN, one frame later, we remove the turbo-loading class, which allows the fade in
    requestAnimationFrame(() => {
        document.body.classList.remove('turbo-loading');
    });
});