import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        dark: localStorage.getItem('maraki-theme') === 'dark'
            || (! localStorage.getItem('maraki-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        init() {
            this.apply();
        },
        toggle() {
            this.dark = ! this.dark;
            localStorage.setItem('maraki-theme', this.dark ? 'dark' : 'light');
            this.apply();
        },
        apply() {
            document.documentElement.classList.toggle('dark', this.dark);
        },
    });
});

Alpine.start();

document.documentElement.classList.toggle(
    'dark',
    localStorage.getItem('maraki-theme') === 'dark'
        || (! localStorage.getItem('maraki-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Installability still works once HTTPS + manifest are available.
        });
    });
}

window.marakiInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    window.marakiInstallPrompt = event;
    window.dispatchEvent(new CustomEvent('maraki-install-available'));
});
