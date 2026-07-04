"use strict";

/*
|--------------------------------------------------------------------------
| Profile Module
|--------------------------------------------------------------------------
|
| Handles
|
| 1. Profile Photo Preview
| 2. Image Validation
| 3. Form Validation
| 4. RSA Encryption
| 5. AJAX Save
|
|--------------------------------------------------------------------------
*/

$(function () {
    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */
    initializeProfile();
});

/*
|--------------------------------------------------------------------------
| Initialize Profile
|--------------------------------------------------------------------------
*/
function initializeProfile() {
    initializeAvatarUpload();
    initializeCancelButton();
}

/*
|--------------------------------------------------------------------------
| Avatar Upload
|--------------------------------------------------------------------------
*/
function initializeAvatarUpload() {

    /*
    |--------------------------------------------------------------------------
    | Open File Browser
    |--------------------------------------------------------------------------
    */

    $(document).on("click","[data-fl-avatar-btn]",function () {
            $(this)
                .closest("[data-fl-avatar]")
                .find("[data-fl-avatar-input]")
                .trigger("click");
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Preview Image
    |--------------------------------------------------------------------------
    */

    $(document).on("change","[data-fl-avatar-input]",function () {
            previewAvatar(this);
        }
    );
}

/*
|--------------------------------------------------------------------------
| Preview Avatar
|--------------------------------------------------------------------------
*/
function previewAvatar(input) {
    if (!input.files.length) {
        return;
    }
    const file = input.files[0];
    /*
    |--------------------------------------------------------------------------
    | Validate Image
    |--------------------------------------------------------------------------
    */
    if (!validateAvatar(file)) {
        input.value = "";
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const container = $(input)
            .closest("[data-fl-avatar]");
        container
            .find(".fl-avatar-img")
            .attr("src", e.target.result)
            .removeClass("hidden");
        container
            .find(".fl-avatar-empty")
            .addClass("hidden");
    };
    reader.readAsDataURL(file);
}

/*
|--------------------------------------------------------------------------
| Validate Avatar
|--------------------------------------------------------------------------
*/

function validateAvatar(file) {
    const allowed = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "image/webp"
    ];

    /*
    |--------------------------------------------------------------------------
    | File Type
    |--------------------------------------------------------------------------
    */
    if (!allowed.includes(file.type)) {
        showErrorAlert(
            "Only JPG, PNG, GIF and WEBP images are allowed."
        );
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | File Size
    |--------------------------------------------------------------------------
    */

    if (file.size > 2 * 1024 * 1024) {
        showErrorAlert(
            "Maximum image size is 2 MB."
        );
        return false;
    }
    return true;
}

/*
|--------------------------------------------------------------------------
| Cancel Button
|--------------------------------------------------------------------------
*/

function initializeCancelButton() {
    $(".btn-outline").on(
        "click",
        function () {
            location.reload();
        }
    );
}

/*
|--------------------------------------------------------------------------
| Form Validation
|--------------------------------------------------------------------------
*/
$(function () {
    $.validator.addMethod(
        "fullName",
        function (value, element) {
            return this.optional(element) ||
                /^[A-Za-z ]+$/.test(value.trim());
        },
        "Please enter a valid name using letters and spaces only."
    );
    $("#profileForm").validate({
        errorElement: "small",
        errorClass: "text-danger d-block mt-1",
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        errorPlacement: function (error, element) {
            if (element.attr("type") === "file") {
                error.insertAfter(element.closest("[data-fl-avatar]"));
            } else {
                error.insertAfter(element.closest(".relative"));
            }
        },
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 150,
                fullName: true
            },
            mobile: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 13
            },
            profile_image: {
                extension: "jpg|jpeg|png|gif|webp"
            }
        },
        messages: {
            username: {
                required: "Please enter username.",
                minlength: "Username must contain at least 3 characters.",
                maxlength: "Username cannot exceed 150 characters."
            },
            mobile: {
                required: "Please enter mobile number.",
                digits: "Only numeric values are allowed.",
                minlength: "Minimum 10 digits required.",
                maxlength: "Maximum 13 digits allowed."
            },
            profile_image: {
                extension: "Only JPG, PNG, GIF and WEBP images are allowed."
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            saveProfile(form);
        }
    });
});

/*
|--------------------------------------------------------------------------
| Save Profile
|--------------------------------------------------------------------------
*/
async function saveProfile(form) {
    const submitButton = $(form).find("button[type='submit']");
    const originalHtml = submitButton.html();
    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */
    submitButton.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

    /*
    |--------------------------------------------------------------------------
    | Encrypt Form
    |--------------------------------------------------------------------------
    */
    let formData = await SecurityManager.encryptFormBySelector("#profileForm");
    formData.append("device_fingerprint",DeviceFingerprint.generate());

    /*
    |--------------------------------------------------------------------------
    | AJAX
    |--------------------------------------------------------------------------
    */

    const alertBox = $("#profileAlert");

    $.ajax({
        url: $(form).attr("action"),
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        headers: {
            [APP_CONFIG.CSRF.NAME]: APP_CONFIG.CSRF.HASH
        },
        success: function (response) {
            if (response.success) {
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
                return;
            }
            showErrorAlert(response.message);
        },
        error: function (xhr) {
            let message = "Unable to update profile.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showErrorAlert(message);
        },
        complete: function (response) {
            /*
            |--------------------------------------------------------------------------
            | Refresh CSRF Token
            |--------------------------------------------------------------------------
            */
            if (response.csrfHash) {
                APP_CONFIG.setCSRFHash(response.csrfHash);                            
            }
            submitButton.prop("disabled", false).html(originalHtml);
        }
    });
}

function showErrorAlert(message) {
    const alertBox = $("#profileAlert");
    alertBox.removeClass("d-none").html(`
        <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-red-50 border border-red-200 text-red-800 mb-2">
            <i data-lucide="x-circle" class="lucide-md shrink-0 mt-0-5 text-red-500"></i>
            <div class="flex-1 min-w-0">
                <p class="font-semibold mb-0-5">
                    Error...!!!
                </p>
                <p class="opacity-80 leading-relaxed m-0">
                    ${message}
                </p>
            </div>
        </div>
    `);
}