import './bootstrap';
import IMask from 'imask';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[name="cpf"]').forEach(el => {
        IMask(el, { mask: '000.000.000-00' });
    });

    document.querySelectorAll('input[name="cnpj"]').forEach(el => {
        IMask(el, { mask: '00.000.000/0000-00' });
    });

    // Suporta celular (11 dígitos) e fixo (10 dígitos)
    document.querySelectorAll('input[name="telefone"]').forEach(el => {
        IMask(el, {
            mask: [
                { mask: '(00) 0000-0000' },
                { mask: '(00) 00000-0000' },
            ],
            dispatch(appended, dynamicMasked) {
                const digits = (dynamicMasked.value + appended).replace(/\D/g, '');
                return dynamicMasked.compiledMasks[digits.length > 10 ? 1 : 0];
            }
        });
    });
});
