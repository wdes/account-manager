<?php
declare(strict_types=1);

namespace AccountManager;
use \stdClass;
use \Exception;
use \Symfony\Component\Dotenv\Dotenv;

class Config {
    protected $dotenv;
    public function __construct(string $envFile) {
        $this->dotenv = new Dotenv();
        if (is_file($envFile)) {
            $this->dotenv->load($envFile);
        } else {
            throw new Exception("The file does not exist : $envFile");
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
        return $dbConfig;
    }
}