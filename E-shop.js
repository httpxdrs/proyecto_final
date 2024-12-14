// Variables
const profile = document.getElementById("profile");
const favorites = document.getElementById("favorites");
const cart = document.getElementById("cart");
const carrito = document.getElementById("carrito");
const loginModal = document.getElementById("loginModal");

document.addEventListener('DOMContentLoaded', () => {
  const inputBusqueda = document.getElementById('busqueda');
  const contenedorSugerencias = document.getElementById('sugerencias');

  inputBusqueda.addEventListener('input', () => {
      const query = inputBusqueda.value.trim();
      if (query.length > 2) {
          fetch(`buscar_sugerencias.php?query=${encodeURIComponent(query)}`)
              .then(response => response.json())
              .then(data => {
                  contenedorSugerencias.innerHTML = '';
                  if (data.length > 0) {
                      contenedorSugerencias.style.display = 'block';
                      data.forEach(producto => {
                          const enlace = document.createElement('a');
                          enlace.href = `resultados.php?q=${encodeURIComponent(producto.nombre_producto)}`;
                          enlace.textContent = producto.nombre_producto;
                          contenedorSugerencias.appendChild(enlace);
                      });
                  } else {
                      contenedorSugerencias.style.display = 'none';
                  }
              })
              .catch(error => console.error('Error:', error));
      } else {
          contenedorSugerencias.style.display = 'none';
      }
  });

  document.addEventListener('click', (e) => {
      if (!contenedorSugerencias.contains(e.target)) {
          contenedorSugerencias.style.display = 'none';
      }
  });
});



// Mostrar el modal de login
profile.addEventListener("click", () => {
  loginModal.style.display = "flex";
});
favorites.addEventListener("click", () => {
  loginModal.style.display = "flex";
});
cart.addEventListener("click", () => {
  loginModal.style.display = "flex";
});
carrito.addEventListener("click", () => {
  loginModal.style.display = "flex";
});
// Cerrar el modal al hacer clic fuera
loginModal.addEventListener("click", (e) => {
  if (e.target === loginModal) {
    loginModal.style.display = "none";
  }
});
function closeLoginModal() {
    loginModal.style.display = "none";
  }
  