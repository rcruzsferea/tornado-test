<?php
    namespace Entity;
    use Spot\EventEmitter;

    /**
     * Entidad de usuarios
     *
     * @property int    $id
     * @property string $nombre
     * @property string $username
     * @property string $email
     * @property string $password
     * @property string $created_at
     */
    class User extends \Spot\Entity
    {
        protected static $table = 'users';

        public static function fields()
        {
            return [
                'id'         => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'name'     => ['type' => 'string', 'required' => true],
                'username'   => ['type' => 'string', 'required' => true, 'unique' => true],
                'email'      => ['type' => 'string', 'required' => true, 'unique' => true],
                'password'   => ['type' => 'string', 'required' => true],
                'created_at' => ['type' => 'datetime', 'required' => true],
            ];
        }

        /**
         * Establece la fecha de creacion automaticamente al insertar
         */
        public static function events(EventEmitter $eventEmitter)
        {
            $eventEmitter->on('beforeInsert', function ($entity, $mapper) {
                if (empty($entity->created_at)) {
                    $entity->created_at = new \DateTime();
                }
            });
        }
    }
