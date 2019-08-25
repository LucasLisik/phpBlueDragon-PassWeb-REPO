<?php

/**
 * Encrypts data and files using AES CBC/CFB, 128/192/256 bits.
 * @author Tasos M. Adamopoulos
 */
class AesEncryption {
    private $modes = array(
        "CBC" => "AES-%d-CBC", "CFB" => "AES-%d-CFB8"
    );
    private $sizes = array(128, 192, 256);
    private $saltLen = 16;
    private $ivLen = 16;
    private $macLen = 32;
    private $blockLen = 16;
    private $mode;
    private $keyLen;

    /** The number of kdf iterations */
    public $keyIterations = 20000;

    /** Accept / return base64 encoded data */
    public $base64 = true;
    
    /**
     * @param string $mode AES mode (CBC, CFB)
     * @param int $size key size (128, 192, 256)
     * @throws UnexpectedValueException when mode or size is invalid
     */
    public function __construct($mode = "CBC", $size = 256) {
        $mode = strtoupper($mode);
        if(!array_key_exists($mode, $this->modes)) {
            throw new UnexpectedValueException("Unsupported mode: $mode\n");
        }
        if(!in_array($size, $this->sizes)) {
            throw new UnexpectedValueException("Invalid key size.\n");
        }
        $this->mode = $mode;
        $this->keyLen = $size / 8;
    }
    
    /**
     * Encrypts data, returns raw bytes or base64 encoded string. 
     * @param string $data 
     * @param string $password
     * @return string encrypted data (salt + iv + ciphertext + hmac)
     */
    public function encrypt($data, $password) {
        $salt = $this->randomBytes($this->saltLen);
        $iv = $this->randomBytes($this->ivLen);
        list($aesKey, $macKey) = $this->keys($password, $salt);
        
        $method = $this->cipher();       
        $ciphertext = openssl_encrypt($data, $method, $aesKey, true, $iv);    
        
        if($ciphertext === false) {
            $this->errorHandler("Encryption failed.\n");
            return null;
        }
        $mac = $this->sign($iv.$ciphertext, $macKey); 
        $encrypted = $salt . $iv . $ciphertext . $mac;
        
        if($this->base64) 
            $encrypted = base64_encode($encrypted);
        return $encrypted;
    }
    
    /**
     * Decrypts data with the supplied password. 
     * @param string $data base64 encoded or raw bytes
     * @param string $password
     * @return string decrypted data
     */
    public function decrypt($data, $password) {
        try {
            $data = $this->base64 ? base64_decode($data, true) : $data;
            if($data === false) {
                throw new UnexpectedValueException("Invalid data format.\n");
            }
            
            list($salt, $iv, $ciphertext, $mac) = array(
                mb_substr($data, 0, $this->saltLen, "8bit"), 
                mb_substr($data, $this->saltLen, $this->ivLen, "8bit"), 
                mb_substr($data, $this->saltLen + $this->ivLen, -$this->macLen, "8bit"), 
                mb_substr($data, -$this->macLen, $this->macLen, "8bit")
            );
            list($aesKey, $macKey) = $this->keys($password, $salt);
            $this->verify($iv.$ciphertext, $mac, $macKey);
            
            $method = $this->cipher();
            $decrypted = openssl_decrypt($ciphertext, $method, $aesKey, true, $iv);
            
            if($decrypted === false) {
                throw new UnexpectedValueException("Decryption failed.\n");
            }
            return $decrypted;
        } catch(Exception $e) {
            $this->errorHandler($e);
        }
    }
    
    
    /**
     * Creates a pair of keys, for encryption and autthentication.
     */
    private function keys($password, $salt) {
        $keyBytes = openssl_pbkdf2(
            $password, $salt, $this->keyLen * 2, $this->keyIterations, "SHA256"
        );
        $keys = array(
            mb_substr($keyBytes, 0, $this->keyLen, "8bit"), 
            mb_substr($keyBytes, $this->keyLen, $this->keyLen, "8bit")
        );
        return $keys;
    }

    /**
     * Creates random bytes for IV and salt generation.
     */
    private function randomBytes($size = 16) {
        return openssl_random_pseudo_bytes($size);
    }

    /**
     * Returns the cipher method of openssl.
     */
    private function cipher() {
        return sprintf($this->modes[$this->mode], $this->keyLen * 8);
    }

    /**
     * Creates MAC signature of data; using HMAC-SHA256.
     */
    private function sign($data, $key) {
        return hash_hmac("SHA256", $data, $key, true);
    }
     
    /**
     * Verifies that the MAC is valid.
     * @throws UnexpectedValueException when MAC is invalid
     */
    private function verify($data, $mac, $key) {
        $dataMac = $this->sign($data, $key);
        
        if(is_callable("hash_equals") && !hash_equals($mac, $dataMac)) {
            throw new UnexpectedValueException("MAC check failed1.\n");
        }
        elseif(!$this->compareMacs($mac, $dataMac)) {
            throw new UnexpectedValueException("MAC check failed2.\n");
        }
    }
        
    /**
     * Handles exceptions (prints the error message by default)
     */
    private function errorHandler($exception) {
        $msg = (gettype($exception) == "string") ? $exception : $exception->getMessage();
        echo $msg;
    }
       
    /**
     * Checks if the two MACs are equal; using constant time comparison.
     */
    private function compareMacs($mac1, $mac2) {
        $result = mb_strlen($mac1, "8bit") ^ mb_strlen($mac2, "8bit");
        $minLen = min(mb_strlen($mac1, "8bit"), mb_strlen($mac2, "8bit"));

        for ($i = 0; $i < $minLen; $i++) {
            $result |= ord($mac1[$i]) ^ ord($mac2[$i]);
        }
        return $result == 0;
    }
}

?>