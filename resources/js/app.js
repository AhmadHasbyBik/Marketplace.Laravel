import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.toast = () => ({
    visible: true,
    timer: null,
    init() {
        this.timer = setTimeout(() => {
            this.visible = false;
        }, 4500);
    },
    close() {
        clearTimeout(this.timer);
        this.visible = false;
    },
});

Alpine.start();
