function validarFormularioTarjeta() {
    const numeroTarjeta = document.getElementById("numero_tarjeta").value;
    const cvv = document.getElementById("cvv").value;

    // Validar que el número de tarjeta tenga 16 dígitos
    if (!/^\d{16}$/.test(numeroTarjeta)) {
        alert("El número de tarjeta debe tener 16 dígitos.");
        return false;
    }

    // Validar que el CVV tenga 3 dígitos
    if (!/^\d{3}$/.test(cvv)) {
        alert("El CVV debe tener 3 dígitos.");
        return false;
    }

    return true;
}
