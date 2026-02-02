<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    public $secretKey = 'your-secret-key-here-change-this';
    public $algorithm = 'HS256';
    public $expiration = 86400; // 24 hours
}