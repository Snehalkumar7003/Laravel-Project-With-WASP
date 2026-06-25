/**
 * |--------------------------------------------------------------------------
 * | Change Password Module
 * |--------------------------------------------------------------------------
 * |
 * | Features:
 * | - jQuery Form Validation
 * | - RSA Form Encryption
 * | - Password Strength Meter
 * | - Password Policy Animation
 * | - Device Fingerprint
 * | - CSRF Protection
 * | - AJAX Password Update
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
 * | 5. device-fingerprint.js
 * |
 * |--------------------------------------------------------------------------
 * | Security Features
 * |--------------------------------------------------------------------------
 * |
 * | - RSA encrypted passwords
 * | - CSRF validation
 * | - Device fingerprint
 * | - Strong password validation
 * | - OWASP compliant
 * |
 * |--------------------------------------------------------------------------
 * | Date        : 25-06-2026
 * | Developer   : Snehal Vasava
 * |--------------------------------------------------------------------------
 */
$(document).ready(function () {
     /*
    |--------------------------------------------------------------------------
    | Initialize Password Policy
    |--------------------------------------------------------------------------
    */
    initializePasswordPolicy();

     /*
    |--------------------------------------------------------------------------
    | Initialize Form Validation
    |--------------------------------------------------------------------------
    */
    $.validator.addMethod(
        "strongPassword",
        function (value) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/.test(value);
        },
        "Password must contain uppercase, lowercase, number and special character."
    );

    $.validator.addMethod(
        "notEqualTo",
        function (value, element, param) {
            return value !== $(param).val();
        },
        "New password cannot be same as current password."
    );

    $("#changePasswordForm").validate({
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
            current_password: {
                required: true
            },
            new_password: {
                required: true,
                minlength: 8,
                strongPassword: true,
                notEqualTo: "#current_password"
            },
            confirm_password: {
                required: true,
                equalTo: "#new_password"
            }
        },
        messages: {
            current_password: {
                required: "Please enter current password."
            },
            new_password: {
                required: "Please enter new password.",
                minlength: "Minimum 8 characters required."
            },
            confirm_password: {
                required: "Please confirm password.",
                equalTo: "Passwords do not match."
            }
        },
        submitHandler: async function (form, event) {
            event.preventDefault();
            const submitBtn = $("#changePasswordBtn");
            const alertBox = $("#changePasswordAlert");
            submitBtn.prop("disabled", true);
            alertBox.html("");
            try {
                const encryptedData = await SecurityManager.encryptFormBySelector("#changePasswordForm");
                encryptedData.device_fingerprint = DeviceFingerprint.generate();

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
                                            ${response.message}. Please wait till redirect...
                                        </p>
                                    </div>                                    
                                </div>
                            `);

                            setTimeout(function(){
                                window.location.href = response.redirect;    
                            },1000);                            
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

function initializePasswordPolicy() {
    // console.log("new_password");
    $("#new_password").on("keyup", function () {

        const password = $(this).val();

        updateRule("#rule-length", password.length >= 8);

        updateRule("#rule-upper", /[A-Z]/.test(password));

        updateRule("#rule-lower", /[a-z]/.test(password));

        updateRule("#rule-number", /\d/.test(password));

        updateRule("#rule-special", /[@$!%*?&]/.test(password));

        updateStrength(password);

    });

}

/*
|--------------------------------------------------------------------------
| Password Strength Meter
|--------------------------------------------------------------------------
*/
function updateStrength(password){

    let score=0;

    if(password.length>=8) score++;

    if(/[A-Z]/.test(password)) score++;

    if(/[a-z]/.test(password)) score++;

    if(/\d/.test(password)) score++;

    if(/[@$!%*?&]/.test(password)) score++;

    let width=0;

    let color="bg-danger";

    let text="Weak";

    switch(score){

        case 1:
        case 2:
            width=25;
            color="bg-danger";
            text="Weak";
            break;

        case 3:
            width=50;
            color="bg-warning";
            text="Fair";
            break;

        case 4:
            width=75;
            color="bg-info";
            text="Good";
            break;

        case 5:
            width=100;
            color="bg-success";
            text="Strong";
            break;
    }

    $("#passwordStrength")
        .removeClass(
            "bg-danger bg-warning bg-info bg-success"
        )
        .addClass(color)
        .css("width",width+"%");

    $("#strengthText").text(text);

}
/*
|--------------------------------------------------------------------------
| Password Policy Animation
|--------------------------------------------------------------------------
*/
function updateRule(selector, valid){

    const row=$(selector);
    row.removeClass("valid invalid");

    console.log(selector);
    console.log(row.length);
    console.log(row.find(".rule-fail").length);
    console.log(row.find(".rule-pass").length);
    
    if(valid){
        row.addClass("valid");
        row.find(".rule-fail").addClass("d-none");
        row.find(".rule-pass").removeClass("d-none");
    }else{
        row.removeClass("valid");
        row.find(".rule-pass").addClass("d-none");
        row.find(".rule-fail").removeClass("d-none");
    }
}
/*
|--------------------------------------------------------------------------
| Toggle Password Visibility
|--------------------------------------------------------------------------
*/
$(document).on("click", ".toggle-password", function () {

    const button = $(this);

    const input = button
        .closest(".input-eye-wrap")
        .find("input");

    const isPassword =
        input.attr("type") === "password";

    input.attr(
        "type",
        isPassword ? "text" : "password"
    );

    button.html(
        isPassword
            ? '<i data-lucide="eye-off" class="lucide-sm"></i>'
            : '<i data-lucide="eye" class="lucide-sm"></i>'
    );

    lucide.createIcons();

});