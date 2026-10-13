import { registrarUsuario } from "./services/index.js";

const $form = document.getElementById('registrar-usuario');
const $inputName = document.getElementById('name');
const $inputEmail = document.getElementById('email');
const $inputUsername = document.getElementById('username');
const $inputPassword = document.getElementById('password');

$form.addEventListener('submit', async (e) => {
    e.preventDefault();
    console.log("enviando")
    const formValues = new FormData($form);
    const dataForm = Object.fromEntries(formValues);
    await registrarUsuario(dataForm);

})