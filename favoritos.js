document.addEventListener("DOMContentLoaded", () => {
    const favoritosModal = document.querySelector("#favoritosModal");
    const abrirFavoritos = document.querySelector("#abrirFavoritos");
    const cerrarFavoritos = document.querySelector("#cerrarFavoritos");

    // Abrir el modal de favoritos
    abrirFavoritos.addEventListener("click", () => {
        favoritosModal.style.display = "block";
        cargarFavoritos(); // Cargar favoritos dinámicamente
    });

    // Cerrar el modal de favoritos
    cerrarFavoritos.addEventListener("click", () => {
        favoritosModal.style.display = "none";
    });

    // Función para cargar favoritos
    const cargarFavoritos = () => {
        fetch("obtenerFavoritos.php")
            .then(response => response.json())
            .then(data => {
                const container  = document.querySelector("#favoritos-lista");
                container .innerHTML = ""; // Limpia el contenido
                if (data.success && data.favoritos.length > 0) {
                    data.favoritos.forEach(favorito => {
                        const card = document.createElement("div");
                        card.classList.add("col-md-4", "d-flex", "align-items-stretch");

                        card.innerHTML = `
                            <div class="card">
                                <img src="${favorito.foto_url}" class="card-img-top" alt="${favorito.nombre_producto}" style="max-height: 150px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">${favorito.nombre_producto}</h5>
                                    <p class="card-text">$${parseFloat(favorito.precio).toFixed(2)}</p>
                                    <a href="descripcionProductos.php?id=${favorito.id_productos}" class="btn btn-primary btn-sm mb-2">Ver Producto</a>
                                </div>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                } else {
                    container .innerHTML = "<li>No tienes favoritos aún.</li>";
                }
            })
            .catch(error => {
                console.error("Error al cargar favoritos:", error);
                const container  = document.querySelector("#favoritos-lista");
                container .innerHTML = "<li>Error al cargar favoritos.</li>";
            });
    };
});

    // Manejar clic y doble clic en botones de favoritos
    document.body.addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-favorito")) {
            e.preventDefault();
            const boton = e.target;
            const idProducto = boton.getAttribute("data-id");

            if (!boton.classList.contains("favorito-activo")) {
                // Agregar a favoritos con un clic
                fetch("agregarFavoritos.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ idProducto: idProducto })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            boton.classList.add("favorito-activo");
                            cargarFavoritos();
                        } else {
                            alert(data.error || "Hubo un problema al agregar a favoritos.");
                        }
                    })
                    .catch(error => {
                        console.error("Error al agregar a favoritos:", error);
                    });
            }
        }
    });

    document.body.addEventListener("dblclick", (e) => {
        if (e.target.classList.contains("btn-favorito")) {
            e.preventDefault();
            const boton = e.target;
            const idProducto = boton.getAttribute("data-id");

            if (boton.classList.contains("favorito-activo")) {
                // Eliminar de favoritos con doble clic
                fetch("eliminarFavoritos.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ idProducto: idProducto })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            boton.classList.remove("favorito-activo");
                            cargarFavoritos();
                        } else {
                            alert(data.error || "Hubo un problema al eliminar de favoritos.");
                        }
                    })
                    .catch(error => {
                        console.error("Error al eliminar de favoritos:", error);
                    });
            }
        }
    });



