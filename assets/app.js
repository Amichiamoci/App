import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('Importing Bootstrap 5 styles and scripts');
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './styles/bootstrap-nightshade.css';
import './theme-handling.js';

import './scroll.js';