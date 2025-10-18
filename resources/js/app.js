import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();

Swal.mixin({
    customClass: {
        popup: 'rounded-2xl shadow-xl',
        title: 'font-bold text-lg text-gray-800',
        confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md',
        cancelButton: 'bg-gray-300 text-black px-4 py-2 rounded-md'
    },
    buttonsStyling: false
});


