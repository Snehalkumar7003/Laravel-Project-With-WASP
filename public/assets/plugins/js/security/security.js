/**
 * |--------------------------------------------------------------------------
 * | Security Manager
 * |--------------------------------------------------------------------------
 * |
 * | This utility handles RSA encryption operations globally across
 * | the application using the server public key.
 * |
 * | Features:
 * | - Dynamically loads RSA public key
 * | - Encrypts individual values
 * | - Encrypts complete form objects
 * | - Reuses cached public key for performance
 * | - Centralized security implementation
 * |
 * | Security Flow:
 * | Frontend
 * |    ↓
 * | Load Public Key (public.pem)
 * |    ↓
 * | Encrypt Sensitive Data
 * |    ↓
 * | Send Encrypted Payload
 * |    ↓
 * | Backend Decrypts Using Private Key
 * |
 * | Usage:
 * |
 * | Encrypt Single Value:
 * | ----------------------------------
 * | const encrypted =
 * |      await SecurityManager.encrypt("admin");
 * |
 * |
 * | Encrypt Form Object:
 * | ----------------------------------
 * | const encryptedData =
 * |      await SecurityManager.encryptForm({
 * |          username: "admin",
 * |          password: "123456"
 * |      });
 * |
 * |--------------------------------------------------------------------------
 * | Dependencies
 * |--------------------------------------------------------------------------
 * |
 * | Required Library:
 * | - JSEncrypt
 * |
 * | Include Before This File:
 * |
 * | <script src="https://cdn.jsdelivr.net/npm/jsencrypt/bin/jsencrypt.min.js"></script>
 * |
 * |--------------------------------------------------------------------------
 * | Public Key Location
 * |--------------------------------------------------------------------------
 * |
 * | public/public.pem
 * |
 * |--------------------------------------------------------------------------
 * | Date        : 22-08-2025
 * | Developer   : Snehal Vasava
 * |--------------------------------------------------------------------------
 */

let PUBLIC_KEY = null;

class SecurityManager {

    /*
    |--------------------------------------------------------------------------
    | Load Public Key
    |--------------------------------------------------------------------------
    |
    | Loads and caches RSA public key from server.
    | Prevents repeated HTTP requests by storing key in memory.
    |
    */
    static async loadPublicKey() {
        // Return cached key if already loaded
        if (PUBLIC_KEY !== null) {
            return PUBLIC_KEY;
        }
        try {
            // Fetch public key from server
            const response = await fetch(APP_CONFIG.BASE_URL + "/auth/public-key");
            // Store in cache
            PUBLIC_KEY = await response.json();
            return PUBLIC_KEY;
        } catch (error) {
            console.error("Unable to load public key", error);
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Encrypt Single Data
    |--------------------------------------------------------------------------
    |
    | Encrypts a single string value using RSA public key.
    |
    | @param string data
    | @return encrypted string
    |
    */
    static async encrypt(data) {
        const publicKey = await this.loadPublicKey();
        if (!publicKey) {
            throw new Error("Public key not loaded");
        }

        //console.log("csrf-token-hash:::"+publicKey.csrf_token);
        // APP_CONFIG.setCSRFHash(publicKey.csrf_token);
        const encrypt = new JSEncrypt();
        encrypt.setPublicKey(publicKey.public_key);
        return encrypt.encrypt(data);
    }

    /*
    |--------------------------------------------------------------------------
    | Encrypt Form Object
    |--------------------------------------------------------------------------
    |
    | Encrypts all key-value pairs from form object.
    |
    | Example:
    |
    | await SecurityManager.encryptForm({
    |     username: "admin",
    |     password: "123456"
    | });
    |
    */
    static async encryptForm(formData) {
        let encryptedData = {};
        for (const key in formData) {
            encryptedData[key] = await this.encrypt(formData[key]);
        }
        return encryptedData;
    }

    /*
    |--------------------------------------------------------------------------
    | Encrypt Form By Selector
    |--------------------------------------------------------------------------
    |
    | Automatically encrypts form fields containing:
    |
    | data-encrypt="true"
    |
    | Example:
    |
    | <input type="password"
    |        name="password"
    |        data-encrypt="true">
    |
    */
    // static async encryptFormBySelector(formSelector) {
    //     // console.log("encryptFormBySelector");
    //     let encryptedData = {};

    //     const fields = $(formSelector)
    //         .find("[data-encrypt='true']");

    //     for (let i = 0; i < fields.length; i++) {

    //         const field = $(fields[i]);
    //         // console.log(field.attr("name"));
    //         // console.log(field.val());
    //         encryptedData[field.attr("name")] =
    //             await this.encrypt(field.val());
    //     }

    //     return encryptedData;
    // }

    /*
    |--------------------------------------------------------------------------
    | Encrypt Form By Selector
    |--------------------------------------------------------------------------
    |
    | Returns FormData with encrypted fields.
    |
    */

    static async encryptFormBySelector(formSelector) {
        const form = $(formSelector)[0];
        let formData = new FormData(form);
        const fields = $(formSelector).find("[data-encrypt='true']");
        
        for (let i = 0; i < fields.length; i++) {
            const field = $(fields[i]);
            const name = field.attr("name");
            const encryptedValue = await this.encrypt(field.val());
            formData.set(name, encryptedValue);
        }
        return formData;
    }
}