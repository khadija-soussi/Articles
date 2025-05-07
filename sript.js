document.addEventListener('DOMContentLoaded', () => {
    
    const menuButton = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('aside');
    const body = document.body;
    
    
    let isMenuOpen = false;

    
    const toggleMenu = (e) => {
        e.stopPropagation();
        isMenuOpen = !isMenuOpen;
        
        
        menuButton.classList.toggle('active', isMenuOpen);
        sidebar.classList.toggle('active', isMenuOpen);
        body.style.overflow = isMenuOpen ? 'hidden' : '';
    };

    
    const closeMenu = () => {
        isMenuOpen = false;
        menuButton.classList.remove('active');
        sidebar.classList.remove('active');
        body.style.overflow = '';
    };

    
    menuButton.addEventListener('click', toggleMenu);
    
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !menuButton.contains(e.target)) {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeMenu();
    });

    
    sidebar.addEventListener('touchmove', (e) => {
        if (isMenuOpen) e.preventDefault();
    }, { passive: false });
});