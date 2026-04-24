<?php
// ============================================================
// src/Helpers/JWT.php
//
// What is a JWT?
// --------------
// JWT = JSON Web Token.  It looks like this:
//   xxxxx.yyyyy.zzzzz
//   HEADER.PAYLOAD.SIGNATURE
//
// - HEADER  : tells what algorithm was used  (base64url encoded)
// - PAYLOAD : the actual data (user id, expiry…) (base64url encoded)
// - SIGNATURE : HMAC of header+payload using our secret key
//
// Anyone can READ the payload (it's just base64), but they cannot
// FAKE or MODIFY it without knowing our secret — because the
// signature would not match.
//
// Flow:
//   1. User logs in  → we create a JWT and send it back.
//   2. User calls a protected route → they send the JWT in the
//      Authorization header:  Authorization: Bearer <token>
//   3. We verify the signature and expiry → allow or deny.
// ============================================================

namespace App\Helpers;

class JWT
{
    // ----------------------------------------------------------
    // encode(): Build a signed JWT from an array of claims.
    //
    // $payload example:
    //   ['sub' => 1, 'name' => 'Alice', 'exp' => time() + 3600]
    // ----------------------------------------------------------
    public static function encode(array $payload, string $secret): string
    {
        // 1. Build the header (always the same for HS256)
        $header = self::base64UrlEncode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
        ]));

        // 2. Encode the payload
        $payload = self::base64UrlEncode(json_encode($payload));

        // 3. Create the signature: HMAC-SHA256 of "header.payload"
        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        // 4. Join the three parts with dots
        return "$header.$payload.$signature";
    }

    // ----------------------------------------------------------
    // decode(): Verify a JWT and return its payload.
    //
    // Throws \Exception on any problem (expired, tampered, etc.)
    // ----------------------------------------------------------
    public static function decode(string $token, string $secret): object
    {
        // Split the token into its 3 parts
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid token structure');
        }

        [$header, $payload, $signature] = $parts;

        // 1. Recompute the expected signature
        $expectedSig = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        // 2. Compare signatures (hash_equals prevents timing attacks)
        if (!hash_equals($expectedSig, $signature)) {
            throw new \Exception('Invalid token signature');
        }

        // 3. Decode the payload
        $data = json_decode(self::base64UrlDecode($payload));
        if (!$data) {
            throw new \Exception('Invalid token payload');
        }

        // 4. Check expiry
        if (isset($data->exp) && $data->exp < time()) {
            throw new \Exception('Token has expired');
        }

        return $data;
    }

    // ----------------------------------------------------------
    // base64UrlEncode / Decode
    //
    // Standard base64 uses +, /, and = which are not URL-safe.
    // JWT uses a variant that replaces them: +→- /→_ and strips =
    // ----------------------------------------------------------
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        // Add back the padding characters base64 needs
        $padded = str_pad($data, strlen($data) + (4 - strlen($data) % 4) % 4, '=');
        return base64_decode(strtr($padded, '-_', '+/'));
    }
}
