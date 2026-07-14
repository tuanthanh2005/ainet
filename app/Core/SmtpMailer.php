<?php

class SmtpMailer {
    private $host;
    private $port;
    private $secure; // 'ssl', 'tls', or 'none'
    private $username;
    private $password;
    private $timeout = 10;
    private $errors = [];

    public function __construct(string $host, int $port, string $secure, string $username, string $password) {
        $this->host = $host;
        $this->port = $port;
        $this->secure = strtolower(trim($secure));
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Send email via SMTP
     *
     * @param string $fromEmail Sender email
     * @param string $fromName Sender display name
     * @param string $toEmail Recipient email
     * @param string $subject Email subject
     * @param string $bodyHtml Email HTML content
     * @param array $attachments Array of files: [['path' => '...', 'name' => '...', 'type' => '...']]
     * @return bool True on success, false on failure
     */
    public function send(string $fromEmail, string $fromName, string $toEmail, string $subject, string $bodyHtml, array $attachments = []): bool {
        $this->errors = [];
        $host = $this->host;
        
        if ($this->secure === 'ssl') {
            $host = 'ssl://' . $host;
        }

        $socket = @fsockopen($host, $this->port, $errno, $errstr, $this->timeout);
        if (!$socket) {
            $this->errors[] = "Không thể kết nối đến SMTP Server: $errstr ($errno)";
            return false;
        }

        stream_set_timeout($socket, $this->timeout);

        if (!$this->expect($socket, 220)) {
            fclose($socket);
            return false;
        }

        $localhost = $_SERVER['SERVER_NAME'] ?: 'localhost';
        fwrite($socket, "EHLO $localhost\r\n");
        if (!$this->expect($socket, 250)) {
            fclose($socket);
            return false;
        }

        if ($this->secure === 'tls') {
            fwrite($socket, "STARTTLS\r\n");
            if (!$this->expect($socket, 220)) {
                fclose($socket);
                return false;
            }

            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->errors[] = "Không thể thiết lập mã hóa TLS.";
                fclose($socket);
                return false;
            }

            fwrite($socket, "EHLO $localhost\r\n");
            if (!$this->expect($socket, 250)) {
                fclose($socket);
                return false;
            }
        }

        if ($this->username !== '' && $this->password !== '') {
            fwrite($socket, "AUTH LOGIN\r\n");
            if (!$this->expect($socket, 334)) {
                fclose($socket);
                return false;
            }

            fwrite($socket, base64_encode($this->username) . "\r\n");
            if (!$this->expect($socket, 334)) {
                fclose($socket);
                return false;
            }

            fwrite($socket, base64_encode($this->password) . "\r\n");
            if (!$this->expect($socket, 235)) {
                fclose($socket);
                return false;
            }
        }

        fwrite($socket, "MAIL FROM:<$fromEmail>\r\n");
        if (!$this->expect($socket, 250)) {
            fclose($socket);
            return false;
        }

        fwrite($socket, "RCPT TO:<$toEmail>\r\n");
        if (!$this->expect($socket, 250)) {
            fclose($socket);
            return false;
        }

        fwrite($socket, "DATA\r\n");
        if (!$this->expect($socket, 354)) {
            fclose($socket);
            return false;
        }

        // Build email headers and body (Multipart MIME)
        $boundary = 'Multipart_Boundary_x' . md5(uniqid(microtime(), true)) . 'x';
        
        $encodedSubject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        $encodedFromName = $fromName !== '' ? "=?UTF-8?B?" . base64_encode($fromName) . "?=" : '';
        
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "To: <$toEmail>";
        $headers[] = "From: " . ($encodedFromName !== '' ? "$encodedFromName <$fromEmail>" : $fromEmail);
        $headers[] = "Subject: $encodedSubject";
        $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";
        $headers[] = "Date: " . date('r');
        $headers[] = "Message-ID: <" . md5(uniqid(microtime(), true)) . "@" . $this->host . ">";
        
        $rawEmail = implode("\r\n", $headers) . "\r\n\r\n";
        
        // HTML Body Part
        $rawEmail .= "--$boundary\r\n";
        $rawEmail .= "Content-Type: text/html; charset=UTF-8\r\n";
        $rawEmail .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $rawEmail .= chunk_split(base64_encode($bodyHtml)) . "\r\n";
        
        // Attachments
        foreach ($attachments as $att) {
            if (!empty($att['path']) && is_file($att['path'])) {
                $filename = basename($att['name'] ?: $att['path']);
                // Encode attachment filename in UTF-8
                $encodedFilename = "=?UTF-8?B?" . base64_encode($filename) . "?=";
                $content = file_get_contents($att['path']);
                $type = $att['type'] ?: 'application/octet-stream';
                
                $rawEmail .= "--$boundary\r\n";
                $rawEmail .= "Content-Type: $type; name=\"$encodedFilename\"\r\n";
                $rawEmail .= "Content-Disposition: attachment; filename=\"$encodedFilename\"\r\n";
                $rawEmail .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $rawEmail .= chunk_split(base64_encode($content)) . "\r\n";
            }
        }
        
        $rawEmail .= "--$boundary--\r\n";
        $rawEmail .= ".\r\n";

        fwrite($socket, $rawEmail);
        if (!$this->expect($socket, 250)) {
            fclose($socket);
            return false;
        }

        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        return true;
    }

    private function expect($socket, int $expectedCode): bool {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        $code = (int)substr($response, 0, 3);
        if ($code !== $expectedCode) {
            $this->errors[] = "SMTP Error (Expected $expectedCode, got $code): " . trim($response);
            return false;
        }
        return true;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
