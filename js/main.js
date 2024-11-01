const hamburger = document.querySelector('.nav-control');
const logoDesktop = document.getElementById('logo-desktop');
const logoMobile = document.getElementById('logo-mobile');
// const footer = document.querySelectorAll('div')//.querySelector('.copyright');

var windowWidth = window.innerWidth;

document.addEventListener('DOMContentLoaded', function() {
    var lastScrollTop = 0;
    var header = document.querySelector('.header');
    var headerCornerLeft = document.querySelector('.nav-header');
    const footer = document.querySelector('.footer'); 

    sidebarFechado = true;
    hamburger.addEventListener('click', function() {
        if (windowWidth >= 1200){
            if (sidebarFechado) {
                logoMobile.style.display = 'block';
                logoDesktop.style.display = 'none';
                sidebarFechado = false;

                footer.style.left = '0';
            } else {
                logoDesktop.style.display = 'block'
                logoMobile.style.display = 'none';
                sidebarFechado = true;

                footer.style.left = '9rem';
            }
        }
    });


    window.addEventListener('scroll', function() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // // Mobile remove todo o header
        // if (windowWidth < 768) {
        //     if (scrollTop > 50) {
        //         // Scroll down
        //         headerCornerLeft.classList.add('hidden');
        //         header.classList.add('hidden');
        //     } else {
        //         // Scroll up
        //         headerCornerLeft.classList.remove('hidden');
        //         header.classList.remove('hidden');
        //     }
        // }

        // // Tablet remove somente parte superior, mantendo canto superior esquerdo
        // if (windowWidth < 1200) {
        //     if (scrollTop > 50) {
        //         // Scroll down
        //         header.classList.add('hidden');
        //     } else {
        //         // Scroll up
        //         header.classList.remove('hidden');
        //     }
        // }

        lastScrollTop = scrollTop;
    });
});


