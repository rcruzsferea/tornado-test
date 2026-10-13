<?php

use Entity\User as EntityUser;

/**
 * Controlador de usuarios
 *
 * La entidad asociada se resuelve automaticamente por el nombre de la clase:
 * controlador `User` => `Entity\User` (model/User.php).
 *
 * @author geoskull
 */
class User extends Tornado\Controller {
    public function viewRegistrarUsuario($req, $res) {
        echo $this->renderView($res);
    }
    public function registrarUsuario($req, $res) {
        try {
            if (isset($req->data)) {
                $data = json_decode($req->data['_RAW_HTTP_DATA']);
                $userMapper = $this->spot->mapper(EntityUser::class);
                $user = $userMapper->create([
                    'name' => $data->name,
                    'username' => $data->username,
                    'email' => $data->email,
                    'password' => $data->password,
                ]);
            }
        } catch (\Throwable $th) {
            $this->log->error($th->getMessage());
            throw $th;
        }
    }
}
