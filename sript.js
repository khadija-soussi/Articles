document.addEventListener('DOMContentLoaded', () => {
    // Éléments DOM
    const menuButton = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('aside');
    const body = document.body;
    
    // État initial
    let isMenuOpen = false;

    // Gestionnaire de clic
    const toggleMenu = (e) => {
        e.stopPropagation();
        isMenuOpen = !isMenuOpen;
        
        // Mise à jour des classes
        menuButton.classList.toggle('active', isMenuOpen);
        sidebar.classList.toggle('active', isMenuOpen);
        body.style.overflow = isMenuOpen ? 'hidden' : '';
    };

    // Fermeture du menu
    const closeMenu = () => {
        isMenuOpen = false;
        menuButton.classList.remove('active');
        sidebar.classList.remove('active');
        body.style.overflow = '';
    };

    // Événements
    menuButton.addEventListener('click', toggleMenu);
    
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !menuButton.contains(e.target)) {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeMenu();
    });

    // Empêcher le scroll quand le menu est ouvert
    sidebar.addEventListener('touchmove', (e) => {
        if (isMenuOpen) e.preventDefault();
    }, { passive: false });
});