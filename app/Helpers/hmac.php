<?php
    use Illuminate\Support\Facades\Log;

    if (!function_exists('generateHmac512')) {
        /**
         * Generate HMAC SHA512 signature untuk API authentication
         *
         * Formula: httpMethod + ":" + relativeUrl + ":" + apiKey + ":" + RequestBody.toLowerCase() + ":" + timeStamp
         *
         * @param string $httpMethod HTTP method (GET, POST, PUT, DELETE, etc.)
         * @param string $relativeUrl Relative URL path (e.g., /api/whitelist/check)
         * @param string $apiKey API key untuk authentication
         * @param string $requestBody Request body dalam format JSON
         * @param string $timeStamp Timestamp dalam format ISO 8601 (e.g., 2025-08-08T10:12:27+07:00)
         * @param string $secretKey Secret key untuk HMAC generation
         * @return string Base64 encoded HMAC SHA512 signature
         *
         * @throws InvalidArgumentException jika parameter tidak valid
         */
        function generateHmac512($httpMethod, $relativeUrl, $apiKey, $requestBody, $timeStamp, $secretKey)
        {
            try {
                Log::info('HMAC512 Generator - Input Parameters', [
                    'httpMethod'  => $httpMethod,
                    'relativeUrl' => $relativeUrl,
                    'apiKey'      => $apiKey,
                    'requestBody' => $requestBody,
                    'timeStamp'   => $timeStamp
                ]);

                // Validasi input parameters
                if (empty($httpMethod) || empty($relativeUrl) || empty($apiKey) || empty($timeStamp) || empty($secretKey)) {
                    throw new \InvalidArgumentException('Semua parameter wajib diisi');
                }

                // Normalize HTTP method ke uppercase
                $httpMethod = strtoupper(trim($httpMethod));

                // Buat string untuk di-hash sesuai formula
                $stringToHash = $httpMethod . ':' . $relativeUrl . ':' . $apiKey . ':' . $requestBody . ':' . $timeStamp;

                // Log string yang akan di-hash
                Log::info('HMAC512 Generator - String to Hash', [
                    'stringToHash' => $stringToHash
                ]);

                // Generate HMAC SHA512
                $hmacHash = hash_hmac('sha512', $stringToHash, $secretKey, true);

                // Encode ke Base64
                $base64Hash = base64_encode($hmacHash);


                // Log hasil
                Log::info('HMAC512 Generator - Generated Hash', [
                    'hmacHash' => $base64Hash
                ]);

                return $base64Hash;

            } catch (\Exception $e) {
                // Log error
                Log::error('HMAC512 Generator - Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                throw $e;
            }
        }
    }

    if (!function_exists('validateHmac512')) {
        /**
         * Validasi HMAC SHA512 signature
         *
         * @param string $httpMethod HTTP method
         * @param string $relativeUrl Relative URL path
         * @param string $apiKey API key
         * @param string $requestBody Request body
         * @param string $timeStamp Timestamp
         * @param string $secretKey Secret key
         * @param string $providedSignature Signature yang diberikan untuk divalidasi
         * @return bool True jika signature valid, false jika tidak
         */
        function validateHmac512($httpMethod, $relativeUrl, $apiKey, $requestBody, $timeStamp, $secretKey, $providedSignature)
        {
            try {
                // Log validasi attempt
                Log::info('HMAC512 Validator - Validation Attempt', [
                    'providedSignature' => $providedSignature
                ]);

                // Generate signature yang seharusnya
                $expectedSignature = generateHmac512($httpMethod, $relativeUrl, $apiKey, $requestBody, $timeStamp, $secretKey);

                // Compare signatures menggunakan hash_equals untuk mencegah timing attacks
                $isValid = hash_equals($expectedSignature, $providedSignature);

                // Log hasil validasi
                Log::info('HMAC512 Validator - Validation Result', [
                    'expectedSignature' => $expectedSignature,
                    'providedSignature' => $providedSignature,
                    'isValid'           => $isValid
                ]);

                return $isValid;

            } catch (\Exception $e) {
                // Log error
                Log::error('HMAC512 Validator - Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return false;
            }
        }
    }
