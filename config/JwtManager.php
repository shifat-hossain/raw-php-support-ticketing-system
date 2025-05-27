<?php

class JwtManager
{
    private $secret;
    private $headers;
    private $conn;
    private $table;

    public function __construct($db)
    {
        $this->headers = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];
        $this->secret = 'raw-php-support-ticketing-system';
        $this->conn = $db;
        $this->table = 'access_tokens';
    }

    public function generateToken($payload)
    {
        $headers = $this->encode(json_encode($this->headers));
        $encoded_payload = $this->encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers.$encoded_payload", $this->secret, true);
        $signature = $this->encode($signature);
        
        $token = "$headers.$encoded_payload.$signature";
        $this->store_token($token, $payload);
        return $token;
    }

    private function encode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '='); // base64 encode string
    }


    public function validateToken($token)
    {
        list($header, $payload, $signature) = explode('.', $token);

        $decoded_payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
        $expectedSignature = hash_hmac('sha256', "$header.$payload", $this->secret, true);
        $tokenSignature = base64_decode(str_replace(['-', '_'], ['+', '/'], $signature));

        $query = "SELECT * FROM $this->table WHERE user_id = :user_id AND token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $decoded_payload['id']);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $access_token = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$access_token) {
            return false;
        }
        if ($tokenSignature !== $expectedSignature) {
            return false;
        }

        return $decoded_payload;
    }

    public function store_token($token, $payload) {
        $query = "INSERT INTO $this->table (user_id, token) VALUES (:user_id, :token)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $payload['id']);
        $stmt->bindParam(':token', $token);

        $stmt->execute();
    }
}
