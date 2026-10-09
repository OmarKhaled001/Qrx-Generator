// Livewire 4 injects Alpine; register the small UI helpers before it starts.
document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;

    Alpine.store('theme', {
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            try {
                localStorage.setItem('qrx-theme', this.dark ? 'dark' : 'light');
            } catch {
                // Storage can be unavailable (private mode); the toggle still works for this page.
            }
        },
    });

    Alpine.data('copyable', (text) => ({
        copied: false,
        async copy() {
            try {
                await navigator.clipboard.writeText(text);
                this.copied = true;
                setTimeout(() => (this.copied = false), 2000);
            } catch {
                this.copied = false;
            }
        },
    }));

    Alpine.data('toasts', (initial = []) => ({
        items: [],
        init() {
            initial.forEach((toast) => this.add(toast));
        },
        add({ message, type = 'success' }) {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 4500);
        },
        remove(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
    }));
});
