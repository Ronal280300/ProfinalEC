let menu = document.querySelector('#menu-btn');
let navbar = document.querySelector('.header .navbar');

menu.onclick = () =>{
   menu.classList.toggle('fa-times');
   navbar.classList.toggle('active');
};

window.onscroll = () =>{
   menu.classList.remove('fa-times');
   navbar.classList.remove('active');
};


document.querySelector('#close-edit').onclick = () =>{
   document.querySelector('.edit-form-container').style.display = 'none';
   window.location.href = 'index.php';
};

// Crear una instancia de MutationObserver con una función de devolución de llamada
const observer = new MutationObserver(function(mutationsList, observer) {
  // Realizar acciones cuando se detecten mutaciones
  console.log("Se detectaron mutaciones en el DOM");
});

// Configurar el observer para observar cambios en los nodos hijos y sus atributos
observer.observe(document.body, { childList: true, subtree: true });

// Detener la observación cuando sea necesario (por ejemplo, al salir de la página)
// observer.disconnect();


//Se utiliza para bloquear el boton file-upload en caso de no haber nigun archivo
document.addEventListener('DOMContentLoaded', function() {
   var submitBtn = document.getElementById('submit-btn');
   var nameInput = document.querySelector('input[name="name"]');
   var priceInput = document.querySelector('input[name="price"]');
   var fileInput = document.getElementById('file-upload');

   function checkFields() {
       if (nameInput.value.trim() !== '' && priceInput.value.trim() !== '' && fileInput.files.length > 0) {
           submitBtn.removeAttribute('disabled');
           submitBtn.classList.add('btn-red');
       } else {
           submitBtn.setAttribute('disabled', 'disabled');
           submitBtn.classList.remove('btn-red');
       }
   }

   nameInput.addEventListener('input', checkFields);
   priceInput.addEventListener('input', checkFields);
   fileInput.addEventListener('change', checkFields);
});