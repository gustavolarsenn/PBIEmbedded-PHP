document.addEventListener('DOMContentLoaded', function() {
    var lastScrollTop = 0;
    var header = document.querySelector('.header');
    var headerCornerLeft = document.querySelector('.nav-header');
    // var fullHeader = document.querySelector('#header-wrap');

    var windowWidth = window.innerWidth;

    window.addEventListener('scroll', function() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Mobile remove todo o header
        if (windowWidth < 768) {
            if (scrollTop > 10) {
                // Scroll down
                headerCornerLeft.classList.add('hidden');
                header.classList.add('hidden');
            } else {
                // Scroll up
                headerCornerLeft.classList.remove('hidden');
                header.classList.remove('hidden');
            }
        }

        // Tablet remove somente parte superior, mantendo canto superior esquerdo
        if (windowWidth < 1200) {
            if (scrollTop > 10) {
                // Scroll down
                header.classList.add('hidden');
            } else {
                // Scroll up
                header.classList.remove('hidden');
            }
        }

        lastScrollTop = scrollTop;
    });
});

