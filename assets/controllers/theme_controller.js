import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';
import { setTheme } from '../theme-handling.js';

export default class extends Controller {
    connect() {
        // Top navbar
        $(this.element).children('a.hide-if-same-theme[data-bs-theme-value]').on('click', function () {
            const theme = $(this).attr('data-bs-theme-value');
            console.log(`Setting ${theme} theme`);
            setTheme(theme);
        });

        // Aside menu
        $(this.element).children().children('a[data-bs-theme-value]').on('click', function () {
            const theme = $(this).attr('data-bs-theme-value');
            console.log(`Setting ${theme} theme`);
            setTheme(theme);
        });
    }
}
