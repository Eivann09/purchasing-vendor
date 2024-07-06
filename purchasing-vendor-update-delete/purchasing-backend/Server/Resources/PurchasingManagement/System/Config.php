<?php


namespace Resources\PurchasingManagement\System;

/**
 * Class Config
 * @package Resources\JuanHR\System
 */
class Config
{
    /**
     * Database Connection String
     *
     * @var string[]
     */
    protected array $conn;

    /**
     * Email Sender configuration
     *
     * @var string[]
     */
    protected array $email_config;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        // Database Configuration
        $this->conn = [
            // The hostname of your database server. Often this is ‘localhost’.
            'host' => 'localhost',
            // The username used to connect to the database.
            'username' => 'root',
            // The password used to connect to the database.
            'password' => '',
            // The name of the database you want to connect to.
            'db' => 'p8_purchasing_ojt_db',
            // An optional table prefix which will added to the table name when running Query Builder queries.
            'prefix' => 'tbl_',
            // The character set used in communicating with the database.
            'charset' => 'utf8',
        ];

        // PHPMailer Configuration
        $this->email_config = [
            // Set the SMTP server to send through
            'Host' => 'mail.pixel8.ph',
            // Enable implicit TLS encryption
            'SMTPSecure' => 'ssl',
            // Enable SMTP authentication
            'SMTPAuth' => true,
            // SMTP username
            'Username' => 'dev@pixel8.ph',
            // SMTP password
            'Password' => ']m8y4Hzbpefm',
            // SMTP port
            'Port' => 465,
        ];
    }

    /**
     * dbConnection
     *
     * @return string[]
     */
    public function dbConnection(): array
    {
        return $this->conn;
    }
    

    /**
     * emailConfiguration
     *
     * @return array|string[]
     */
    public function emailConfiguration(): array
    {
        return $this->email_config;
    }
}
