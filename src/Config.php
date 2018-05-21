<?php
declare(strict_types=1);

namespace AccountManager;
use \stdClass;
use \Exception;
use \Dotenv\Dotenv;

class Config {
    protected $dotenv;
    public function __construct(string $envDir) {
        if (is_dir($envDir)) {
            $this->dotenv = new Dotenv($envDir);
            $this->dotenv->load();
        } else {
            throw new Exception("The directory does not exist : $envDir");
        }
    }
    /**
     * Get the Database config
     *
     * @return stdClass the database config
     */
    public function getDatabase(): stdClass {
        $dbConfig = new stdClass();
        $dbConfig->user = getenv('DB_USER');
        $dbConfig->password = getenv('DB_PASS');
        $dbConfig->host = getenv('DB_HOST');
        $dbConfig->name = getenv('DB_NAME');
        return $dbConfig;
    }
}