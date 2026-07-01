"use strict";

/*
|--------------------------------------------------------------------------
| Forgot Password Module
|--------------------------------------------------------------------------
|
| Handles:
| 1. Client-side Validation
| 2. RSA Encryption
| 3. AJAX Request
| 4. Loading State
| 5. Success/Error Message
|
|--------------------------------------------------------------------------
*/

$(function () {
    /*
    |--------------------------------------------------------------------------
    | Form Validation
    |--------------------------------------------------------------------------
    */
    $("#forgot-form").validate({
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
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            }
        },
        /*
        |--------------------------------------------------------------------------
        | Submit Form
        |--------------------------------------------------------------------------
        */
        submitHandler: async function (form, event) {
            event.preventDefault();
            try {
                const submitBtn = $(form).find("button[type='submit']");
                const alertBox = $("#loginAlert");
                const encryptedData = await SecurityManager.encryptFormBySelector(form);
                let buttonText = $("#forgot-btn-text");
                submitBtn.prop("disabled", true);
                buttonText.text("Sending...");

                $.ajax({
                    url: $(form).attr("action"),
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": APP_CONFIG.CSRF.HASH
                    },
                    data: encryptedData,
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
                            alertBox.removeClass("d-none").html(`
                                <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-emerald-50 border border-emerald-200 text-emerald-800">
                                    <i data-lucide="check-circle" class="lucide-md shrink-0 mt-0-5 text-emerald-500"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold mb-0-5">
                                            Success...!!!
                                        </p>

                                        <p class="opacity-80 leading-relaxed m-0">
                                            ${response.message}.
                                        </p>
                                    </div>                                    
                                </div>
                            `);

                            setTimeout(function(){
                                // window.location.href = response.redirect;    
                            },3000);                            
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
                        }
                        if (window.lucide) {
                            lucide.createIcons();
                        }
                    },
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
                    complete: function () {
                        submitBtn.prop("disabled", false);
                        buttonText.text("Send Reset Link");
                    }
                });
            } catch (e) {
                console.error(error);
                 $("#loginAlert").removeClass("d-none").html(`
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
