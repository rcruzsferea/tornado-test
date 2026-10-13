export async function registrarUsuario(dataForm) {
    const data = await fetch('/registrar-usuario', {
        method: 'POST',
        body: JSON.stringify(dataForm),
        headers: {'Content-type': 'application/json'}
    });
    console.log(data)
}