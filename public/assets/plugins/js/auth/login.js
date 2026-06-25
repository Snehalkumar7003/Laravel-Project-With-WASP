/**
 * |--------------------------------------------------------------------------
 * | Login Authentication Module
 * |--------------------------------------------------------------------------
 * |
 * | Features:
 * | - jQuery Form Validation
 * | - RSA Form Encryption
 * | - Device Fingureprint
 * | - CSRF Protection
 * | - AJAX Authentication
 * | - Bootstrap Error Handling
 * | - Loading State Management
 * |
 * |--------------------------------------------------------------------------
 * | Dependencies
 * |--------------------------------------------------------------------------
 * |
 * | 1. jQuery
 * | 2. jquery.validate.min.js
 * | 3. jsencrypt.min.js
 * | 4. security.js
 * |
 * |--------------------------------------------------------------------------
 * | Security Features
 * |--------------------------------------------------------------------------
 * |
 * | - RSA encrypted credentials
 * | - CSRF token validation
 * | - OWASP-friendly structure
 * | - No sensitive global variables
 * |
 * |--------------------------------------------------------------------------
 * | Date        : 22-06-2026
 * | Developer   : Snehal Vasava
 * |--------------------------------------------------------------------------
 */

$(document).ready(function () {
    /*
    |--------------------------------------------------------------------------
    | CSRF Configuration
    |--------------------------------------------------------------------------
    */
    // const CSRF = {
    //     NAME: $('meta[name="csrf-name"]').attr('content'),
    //     HASH: $('meta[name="csrf-hash"]').attr('content')
    // };

    /*
    |--------------------------------------------------------------------------
    | Login Form Validation
    |--------------------------------------------------------------------------
    */
    $.validator.addMethod(
        "strictEmail",
        function (value, element) {
            return this.optional(element)||/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/.test(value);
        },
        "Please enter valid email address."
    );

    $("#loginForm").validate({
        // ignore: [],
        errorElement: "div",
        errorClass: "invalid-feedback",
        highlight: function (element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
        },
        errorPlacement: function (error, element) {
            if (element.closest('.input-eye-wrap').length) {
                error.insertAfter( element.closest('.input-eye-wrap') ); 
            } else { 
                error.insertAfter(element); 
            }
        },
        rules: {
            username: {
                required: true,
                strictEmail: true
            },
            password: {
                required: true,
                // minlength: 6
            }
        },
        messages: {
            username: {
                required:"Please enter email address",
                strictEmail:"Please enter valid email address"
            },
            password: {
                required:"Please enter password",
                minlength: "Password must be at least 6 characters"
            }
        },
        /*
        |--------------------------------------------------------------------------
        | Submit Handler
        |--------------------------------------------------------------------------
        */
        submitHandler: async function (form, event) {
            /*
            |--------------------------------------------------------------------------
            | Prevent Default Form Submission
            |--------------------------------------------------------------------------
            */
            event.preventDefault();
            try {
                /*
                |--------------------------------------------------------------------------
                | UI Elements
                |--------------------------------------------------------------------------
                */
                const submitBtn = $(form).find("button[type='submit']");
                const alertBox = $("#loginAlert");

                /*
                |--------------------------------------------------------------------------
                | Reset Error Message
                |--------------------------------------------------------------------------
                */
                alertBox.addClass("d-none").html("");

                /*
                |--------------------------------------------------------------------------
                | Button Loading State
                |--------------------------------------------------------------------------
                */
                submitBtn.prop("disabled", true);                            
                /*
                |--------------------------------------------------------------------------
                | Encrypt Form Data
                |--------------------------------------------------------------------------
                */            
               const encryptedData = await SecurityManager.encryptFormBySelector("#loginForm");
               encryptedData.device_fingerprint = DeviceFingerprint.generate();            
                /*
                |--------------------------------------------------------------------------
                | Append CSRF Token
                |--------------------------------------------------------------------------
                */
                // encryptedData[APP_CONFIG.CSRF.NAME] = APP_CONFIG.CSRF.HASH;
                /*
                |--------------------------------------------------------------------------
                | AJAX Authentication Request
                |--------------------------------------------------------------------------
                */
                console.log(APP_CONFIG.CSRF.HASH);
                
                $.ajax({
                    url: $(form).attr("action"),
                    type: $(form).attr("method"),
                    dataType: "json",
                    data: encryptedData,
                    headers: {
                        'X-CSRF-TOKEN': APP_CONFIG.CSRF.HASH
                    },
                    /*
                    |--------------------------------------------------------------------------
                    | Success Response
                    |--------------------------------------------------------------------------
                    */
                    success: function (response) {
                        /*
                        |--------------------------------------------------------------------------
                        | Refresh CSRF Token
                        |--------------------------------------------------------------------------
                        */
                        if (response.csrfHash) {
                             APP_CONFIG.setCSRFHash(
                                response.csrfHash
                            );
                            $('meta[name="csrf-hash"]').attr("content",response.csrfHash);
                        }
                        /*
                        |--------------------------------------------------------------------------
                        | Authentication Success
                        |--------------------------------------------------------------------------
                        */
                        if (response.success === 1) {
                            window.location.href = response.redirect;
                        } else {
                            /*
                            |--------------------------------------------------------------------------
                            | Authentication Failed
                            |--------------------------------------------------------------------------
                            */
                            alertBox.removeClass("d-none").html(`
                                <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-red-50 border border-red-200 text-red-800">
                                    <i data-lucide="x-circle" class="lucide-md shrink-0 mt-0-5 text-red-500"></i>

                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold mb-0-5">
                                            Error...!!!
                                        </p>

                                        <p class="opacity-80 leading-relaxed m-0">
                                            ${errorMessage}
                                        </p>
                                    </div>                                    
                                </div>
                            `);

                            if (window.lucide) {
                                lucide.createIcons();
                            }
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Server Error Handling
                    |--------------------------------------------------------------------------
                    */
                    error: function (xhr) {
                        let errorMessage = "Unable to process request";
                        /*
                        |--------------------------------------------------------------------------
                        | HTTP Errors
                        |--------------------------------------------------------------------------
                        */
                        if (xhr.status === 404) {
                            errorMessage = "API endpoint not found";
                        } else if (xhr.status === 500) {
                            errorMessage = "Internal server error";
                        } else {
                            try {
                                /*
                                |--------------------------------------------------------------------------
                                | Parse API Response
                                |--------------------------------------------------------------------------
                                */
                                let response = JSON.parse(xhr.responseText);

                                /*
                                |--------------------------------------------------------------------------
                                | Reset CSRF Token
                                |--------------------------------------------------------------------------
                                */
                                if (response.csrfHash) {
                                    APP_CONFIG.setCSRFHash(
                                        response.csrfHash
                                    );
                                }
                                /*
                                |--------------------------------------------------------------------------
                                | Validation Errors
                                |--------------------------------------------------------------------------
                                */
                                if (typeof response.message === "object") {
                                    errorMessage = Object.values(response.message).join("<br>");
                                } else if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                errorMessage ="Unexpected server response";
                            }
                        }
                        /*
                        |--------------------------------------------------------------------------
                        | Show Error Message
                        |--------------------------------------------------------------------------
                        */
                        alertBox.removeClass("d-none").html(`
                            <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-red-50 border border-red-200 text-red-800">
                                <i data-lucide="x-circle" class="lucide-md shrink-0 mt-0-5 text-red-500"></i>

                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold mb-0-5">
                                        Error...!!!
                                    </p>

                                    <p class="opacity-80 leading-relaxed m-0">
                                        ${errorMessage}
                                    </p>
                                </div>                                    
                            </div>
                        `);

                        if (window.lucide) {
                            lucide.createIcons();
                        }
                    },
                    /*
                    |--------------------------------------------------------------------------
                    | Request Complete
                    |--------------------------------------------------------------------------
                    */
                    complete: function () {
                        submitBtn.prop("disabled",false);                        
                    }
                });
            } catch (error) {
                console.error(error);
                 $("#loginAlert")
                    .removeClass("d-none")
                    .html(`
                        <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-red-50 border border-red-200 text-red-800">
                            <i data-lucide="x-circle" class="lucide-md shrink-0 mt-0-5 text-red-500"></i>

                            <div class="flex-1 min-w-0">
                                <p class="font-semibold mb-0-5">
                                    Security Error
                                </p>

                                <p class="opacity-80 leading-relaxed m-0">
                                    Security validation failed.
                                </p>
                            </div>
                        </div>
                    `);

                if (window.lucide) {
                    lucide.createIcons();
                }
            }

            return false;
        }
    });
});