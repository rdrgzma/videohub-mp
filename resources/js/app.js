import './bootstrap';

// Configurações globais
window.Laravel = {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
};

// Configurar axios se disponível
if (typeof axios !== 'undefined') {
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
}
