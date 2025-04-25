document.addEventListener('DOMContentLoaded', function () {
    const fechaInput = document.getElementById('fecha');
    const horarioSelect = document.getElementById('horario');

    const horariosDisponibles = [
        "14:00",
        "14:40",
        "15:20",
        "16:00",
        "16:40",
        "17:20",
        "18:00",
        "18:40",
        "19:20",
        "20:00",
    ];

    function establecerFechaMinima() {
        const hoy = new Date();
        const dia = hoy.getDate().toString().padStart(2, '0');
        const mes = (hoy.getMonth() + 1).toString().padStart(2, '0');
        const anio = hoy.getFullYear();
        const fechaMinima = `${anio}-${mes}-${dia}`;
        fechaInput.setAttribute('min', fechaMinima);
    }

    function horarioYaPaso(fechaSeleccionada, horario) {
        const ahora = new Date();
        const [horaH, minutoH] = horario.split(":").map(Number);
        const fechaHora = new Date(`${fechaSeleccionada}T${horaH.toString().padStart(2, '0')}:${minutoH.toString().padStart(2, '0')}:00`);

        return fechaHora <= ahora;
    }

    establecerFechaMinima();

    fechaInput.addEventListener('change', function () {
        const fechaSeleccionada = this.value;

        fetch(`index.php?action=obtenerHorariosReservados&fecha=${fechaSeleccionada}`)
            .then(response => response.json())
            .then(horariosReservados => {
                horarioSelect.innerHTML = '';

                horariosDisponibles.forEach(horario => {
                    const option = document.createElement('option');
                    option.value = horario;
                    option.textContent = horario;

                    const reservado = horariosReservados.includes(horario);
                    const pasado = horarioYaPaso(fechaSeleccionada, horario);

                    if (reservado || pasado) {
                        option.disabled = true;
                    }

                    horarioSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener los horarios:', error);
            });
    });
});
