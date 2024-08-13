const scroll_btn = document.querySelectorAll('[data-btn-to-top]')[0];
scroll_btn.onclick = () => {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
window.onscroll = handle_scroll;

function handle_scroll() {
    if (!scroll_btn)
        return;
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        scroll_btn.classList.remove('d-none');
    } else {
        scroll_btn.classList.add('d-none');
    }
}