let default_theme = 'light';

const meta_color_scheme = document.querySelector('meta[name="color-scheme"]');
if (meta_color_scheme) {
    default_theme = meta_color_scheme.getAttribute('content').split(' ')[0];
}

const local_theme = window.localStorage.getItem('theme');
const html = document.documentElement;
const possible_themes = ['light', 'dark'];
let current_theme_index = 0;

function setTheme(theme) {
    console.log('Setting \'' + theme + '\' theme');
    possible_themes.forEach(t => {
        if (t === theme) {
            html.classList.add(t);
        } else {
            html.classList.remove(t);
        }
    });
    if (window.localStorage.getItem('theme') !== theme) {
        window.localStorage.setItem('theme', theme);
    }
    current_theme_index = possible_themes.indexOf(theme);
}

function rotateTheme() {
    current_theme_index = (current_theme_index + 1) % possible_themes.length;
    setTheme(possible_themes[current_theme_index]);
}

const change_themes_links = [...document.querySelectorAll('a[data-change-theme]')];
change_themes_links.forEach(a => {
    a.onclick = evt => {
        evt.preventDefault();
        rotateTheme();
    }
    a.oncontextmenu = evt => evt.preventDefault();
    a.href= "javascript:rotateTheme()";
});



if (local_theme) {
    setTheme(local_theme);
} else {
    setTheme(default_theme);
}
