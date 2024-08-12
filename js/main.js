document.addEventListener('DOMContentLoaded', function() {
    var lastScrollTop = 0;
    var header = document.querySelector('#header-wrap');

    window.addEventListener('scroll', function() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > 10) {
            // Scroll down
            header.classList.add('hidden');
        } else {
            // Scroll up
            header.classList.remove('hidden');
        }

        lastScrollTop = scrollTop;
    });
});

