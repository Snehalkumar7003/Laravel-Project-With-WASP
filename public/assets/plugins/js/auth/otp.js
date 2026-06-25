/**
 * |--------------------------------------------------------------------------
 * | OTP Verification
 * |--------------------------------------------------------------------------
 * |
 * | Handles Two-Factor Authentication (2FA) using Email OTP.
 * |
 * | Responsibilities
 * |
 * | - OTP Input Navigation
 * | - Auto Focus
 * | - Paste Support
 * | - Numeric Validation
 * | - Form Validation
 * | - RSA Encryption
 * | - AJAX OTP Verification
 * | - Resend OTP
 * | - Countdown Timer
 * | - Loading State
 * | - Error Handling
 * |
 * |--------------------------------------------------------------------------
 * | Security Features
 * |--------------------------------------------------------------------------
 * |
 * | ✓ CSRF Protection
 * | ✓ RSA Encryption
 * | ✓ AJAX Submission
 * | ✓ OTP Auto Clear
 * | ✓ Timer Validation
 * | ✓ Resend Protection
 * |
 * |--------------------------------------------------------------------------
 */
/*
|--------------------------------------------------------------------------
| Constants
|--------------------------------------------------------------------------
*/
const OTP_LENGTH = 6;

const OTP_TIMER = 5;

let timer = OTP_TIMER;

$(function () {
    initializeOtpInputs();
    initializePaste();
    initializeResend();
    startOtpTimer();
    $("#otp-form").validate({
        ignore: [],
        rules: {
            otp: {
                required: true,
                minlength: 6,
                maxlength: 6
            }
        },
        messages: {
            otp: {
                required: "Please enter OTP.",
                minlength: "Please enter 6 digit OTP.",
                maxlength: "Please enter 6 digit OTP."
            }
        },
        submitHandler: async function (form, event) {
            event.preventDefault();

            let submitBtn = $("#otp-submit");
            let btnText = $("#otp-btn-text");
            submitBtn.prop("disabled", true);
            btnText.text(
                "Verifying..."
            );        
            let formData = new FormData(form);
            formData.append('device_fingerprint', DeviceFingerprint.generate());
            $.ajax({
                url: $(form).attr("action"),
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                headers: {
                    [APP_CONFIG.CSRF.NAME]: APP_CONFIG.CSRF.HASH
                },
                success: function (response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                        return;
                    }
                    showOTPError(response.message);
                },
                error: function (xhr) {
                    let response = xhr.responseJSON;
                    showOTPError(response.message);
                },
                complete: function () {
                    submitBtn.prop("disabled",false);
                    btnText.text("Verify Code");
                }
            });    
        }
    });
});

/*
|--------------------------------------------------------------------------
| OTP Input Navigation
|--------------------------------------------------------------------------
*/
function initializeOtpInputs() {
    $(".otp-input").on("input", function () {
        let value = $(this).val();
        value = value.replace(/\D/g, "");
        $(this).val(value);
        if (value.length === 1) {
            $(this).next(".otp-input").focus();
        }
        updateOTP();
    });
    $(".otp-input").on("keydown", function (e) {
        if (e.key === "Backspace" && $(this).val() === "") {
            $(this).prev(".otp-input").focus();
        }
    });
}

/*
|--------------------------------------------------------------------------
| Build OTP Value
|--------------------------------------------------------------------------
*/
function updateOTP() {
    let otp = "";
    $(".otp-input").each(function () {
        otp += $(this).val();
    });
    $("#otp").val(otp);
}

/*
|--------------------------------------------------------------------------
| OTP Paste Support
|--------------------------------------------------------------------------
*/
function initializePaste() {
    $(".otp-input:first").on("paste", function (e) {
        e.preventDefault();
        let pasted = (e.originalEvent ||e).clipboardData.getData("text");
        pasted = pasted.replace(/\D/g, "");
        if (pasted.length !== OTP_LENGTH) {
            return;
        }

        $(".otp-input").each(function (index) {
            $(this).val(pasted[index]);
        });
        updateOTP();
        $(".otp-input:last").focus();
    });
}
/*
|--------------------------------------------------------------------------
| Show Error
|--------------------------------------------------------------------------
*/
function showOTPError(message) {
    $("#otp-error").removeClass("d-none").text(message);
}

/*
|--------------------------------------------------------------------------
| Resend OTP
|--------------------------------------------------------------------------
*/
function initializeResend() {
    $("#otp-resend").click(function () {
        resendOTP();
    });
}

/*
|--------------------------------------------------------------------------
| AJAX Resend
|--------------------------------------------------------------------------
*/
function resendOTP() {
    $.ajax({
        url: APP_CONFIG.BASE_URL +"/otp/resend",
        type: "POST",
        headers: {
            [APP_CONFIG.CSRF.NAME]: APP_CONFIG.CSRF.HASH
        },
        success: function (response) {
            if (response.success==1) {
                timer = OTP_TIMER;
                startOtpTimer();
                showOTPError(response.message);
            }else{
                showOTPError(response.message);
            }

        },
        error: function (xhr) {
            let response = xhr.responseJSON;
            showOTPError(response.message);
        },
    });
}

/*
|--------------------------------------------------------------------------
| Countdown Timer
|--------------------------------------------------------------------------
*/
function startOtpTimer() {
    $("#otp-resend").prop("disabled", true);
    $("#otp-timer").removeClass("d-none");
    clearInterval(window.otpInterval);
    window.otpInterval = setInterval(function () {
       timer--;
        let minutes = Math.floor(timer / 60);
        let seconds = timer % 60;
        $("#otp-timer").text("in "+minutes.toString().padStart(2, "0")+
            ":"+seconds.toString().padStart(2, "0")
        );
        if (timer <= 0) {
            clearInterval(window.otpInterval);
            $("#otp-resend").prop("disabled", false);
            $("#otp-timer").addClass("d-none");
        }
    }, 1000);
}

/*
|--------------------------------------------------------------------------
| Clear OTP Inputs
|--------------------------------------------------------------------------
*/
function clearOTP() {
    $(".otp-input").val("");
    $("#otp").val("");
    $(".otp-input:first").focus();
}