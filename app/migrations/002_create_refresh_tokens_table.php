<?php

class Create_refresh_tokens_table {

    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->table_exists('refresh_tokens')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'id' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE,
                ],
                'user_id' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                ],
                'token' => [
                    'type' => 'TEXT',
                    'null' => FALSE,
                ],
                'expires_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE,
                ],
                'jti' => [
                    'type' => 'TEXT',
                    'null' => FALSE,
                ],
            ])
            ->add_key('id', primary: TRUE)
            ->add_key('user_id', name: 'user_id_idx')
            ->create_table('refresh_tokens');
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('refresh_tokens');
    }

    private function table_exists($table)
    {
        if (strtolower(database_config()['main']['driver'] ?? 'mysql') !== 'sqlite') {
            return $this->_lava->dbforge->table_exists($table);
        }
        return (bool) $this->_lava->db->raw(
            "SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = ? LIMIT 1",
            [$table]
        )->fetchColumn();
    }
}
