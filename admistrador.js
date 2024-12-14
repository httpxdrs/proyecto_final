document.addEventListener('DOMContentLoaded', function () {
    const adminIcon = document.getElementById('adminIcon');
    const adminMenu = document.getElementById('adminMenu');

    if (adminIcon && adminMenu) {
        adminIcon.addEventListener('click', () => {
            adminMenu.style.display = adminMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Cerrar el menú al hacer clic fuera de él
        document.addEventListener('click', (event) => {
            if (!adminMenu.contains(event.target) && event.target !== adminIcon) {
                adminMenu.style.display = 'none';
            }
        });
    }
});