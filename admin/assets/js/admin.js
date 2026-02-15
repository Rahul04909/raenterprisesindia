document.addEventListener('DOMContentLoaded', function () {
    // Sidebar Submenu Toggle
    const menuItems = document.querySelectorAll('.menu-item-has-children > a');

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent navigation for toggle items

            const parent = this.parentElement;

            // Close other open menus (optional, WP usually keeps them open)
            // document.querySelectorAll('.menu-item-has-children.open').forEach(openItem => {
            //     if (openItem !== parent) openItem.classList.remove('open');
            // });

            parent.classList.toggle('open');
        });
    });
});
