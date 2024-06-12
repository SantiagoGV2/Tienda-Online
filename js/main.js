



let eliminaModal = document.getElementById('eliminaModal');
eliminaModal.addEventListener('show.bs.modal', function(event){
    let button = event.relatedTarget;
    let id = button.getAttribute('data-bs-id');
    let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-elimina');
    buttonElimina.value = id;
})

function eliminar(){

    let buttonElimina = document.getElementById('btn-elimina');
    let id = buttonElimina.value;

    let url = './clases/actualizar_carrito.php';
    let formData = new FormData();
    formData.append('action', 'eliminar');
    formData.append('id', id);
    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    }).then(response => response.json())
    .then(data =>{
        if(data.ok){
            location.reload();
        }
    })
}