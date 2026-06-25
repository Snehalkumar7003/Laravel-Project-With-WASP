<?php

namespace App\Services\Mail;

use App\Mail\ForgotPasswordMail;
use App\Mail\LoginOtpMail;
use App\Mail\PasswordChangedMail;
use App\Mail\WelcomeMail;
use App\Models\common\TblEmailLogModel;
use App\Models\common\TblLogModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService{
    /*
    |--------------------------------------------------------------------------
    | Send Login OTP Email
    |--------------------------------------------------------------------------
    */
    public static function sendOTP(object $user,string $otp): array {
        return self::send(user: $user, subject: 'Login Verification Code', template: 'OTP', mail: new LoginOtpMail($user,$otp));
    }

    /*
    |--------------------------------------------------------------------------
    | Send Password Changed Email
    |--------------------------------------------------------------------------
    */
    public static function sendPasswordChanged(object $user, string $ipAddress, string $userAgent): array {
        return self::send( user: $user, subject: 'Password Changed Successfully', template: 'PASSWORD_CHANGED', mail: new PasswordChangedMail($user,$ipAddress,$userAgent));
    }

    /*
    |--------------------------------------------------------------------------
    | Send Forgot Password Email
    |--------------------------------------------------------------------------
    */
    public static function sendForgotPassword(object $user,string $token): array {
        return self::send(user    : $user, subject : 'Password Reset Request', template: 'FORGOT_PASSWORD', mail: new ForgotPasswordMail($user,$token));
    }

    /*
    |--------------------------------------------------------------------------
    | Send Welcome Email
    |--------------------------------------------------------------------------
    */
    public static function sendWelcome(object $user): array {
        return self::send(
            user        : $user,
            subject     : 'Welcome',
            template    : 'WELCOME',
            mail        : new WelcomeMail($user)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Common Mail Sender
    |--------------------------------------------------------------------------
    |
    | Responsible for:
    |
    | 1. Create Email Log
    | 2. Render Email
    | 3. Send Email
    | 4. Update Email Log
    | 5. Audit Log
    |
    |--------------------------------------------------------------------------
    */
    private static function send(object $user, string $subject, string $template, $mail): array {
        DB::beginTransaction();
        try {
            /*
            |--------------------------------------------------------------------------
            | Render Email Body
            |--------------------------------------------------------------------------
            */
            $body = self::renderEmail($mail);

            /*
            |--------------------------------------------------------------------------
            | Create Email Log
            |--------------------------------------------------------------------------
            */
            $emailLog = self::createEmailLog(
                user: $user,
                subject: $subject,
                template: $template,
                body: $body
            );

            /*
            |--------------------------------------------------------------------------
            | Send Email
            |--------------------------------------------------------------------------
            |
            | Future Queue:
            |
            | Mail::to(...)->queue($mail);
            |
            |--------------------------------------------------------------------------
            */
            Mail::to($user->email)->send($mail);

            /*
            |--------------------------------------------------------------------------
            | Update Email Log
            |--------------------------------------------------------------------------
            */

            self::updateEmailLog(
                emailLogId: $emailLog->tbl_email_logs_id,
                status: 'SENT'
            );

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */

            self::writeAuditLog(
                user: $user,
                action: 'EMAIL_SENT',
                remarks: "{$template} email sent successfully."
            );
            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            return self::successResponse();

        } catch (Exception $e) {
            DB::rollBack();
            /*
            |--------------------------------------------------------------------------
            | Update Email Log
            |--------------------------------------------------------------------------
            */

            if (isset($emailLog)) {
                self::updateEmailLog(
                    emailLogId: $emailLog->tbl_email_logs_id,
                    status: 'FAILED',
                    errorMessage: $e->getMessage()
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Audit Log
            |--------------------------------------------------------------------------
            */

            self::writeAuditLog(
                user: $user,
                action: 'EMAIL_FAILED',
                remarks: $e->getMessage()
            );

            /*
            |--------------------------------------------------------------------------
            | Laravel Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Email Error : ' .
                $e->getMessage()
            );
            return self::failedResponse();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Email Log
    |--------------------------------------------------------------------------
    |
    | Create email log before sending.
    |
    */
    private static function createEmailLog(object $user, string $subject, string $template, string $body): TblEmailLogModel {
        return TblEmailLogModel::create([
            'mst_users_id'     => $user->mst_users_id,
            'email_to'         => $user->email,
            'email_subject'    => $subject,
            'email_template'   => $template,
            'email_body'       => $body,
            'provider'         => 'RAIDS SMTP',
            'status'           => 'PENDING'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Email Log
    |--------------------------------------------------------------------------
    |
    | Update email delivery status.
    |
    */
    private static function updateEmailLog(int $emailLogId, string $status, ?string $errorMessage = null): void {
        TblEmailLogModel::where(
            'tbl_email_logs_id',
            $emailLogId
        )->update([
            'status'        => $status,
            'error_message' => $errorMessage,
            'sent_at'       => $status === 'SENT' ? now() : null,
            'update_date'   => now()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Audit Log
    |--------------------------------------------------------------------------
    |
    | Store application audit logs.
    |
    */
    private static function writeAuditLog(object $user, string $action, string $remarks): void {
        TblLogModel::create([
            'module'        => 'EMAIL',
            'module_id'     => $user->mst_users_id,
            'mst_users_id'  => $user->mst_users_id,
            'action'        => $action,
            'ip_address'    => request()->ip(),
            'remarks'       => $remarks
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Render Email Body
    |--------------------------------------------------------------------------
    |
    | Render blade template for logging purpose.
    |
    */
    private static function renderEmail($mail): string {
        return $mail->render();
    }

    /*
    |--------------------------------------------------------------------------
    | Success Response
    |--------------------------------------------------------------------------
    */
    private static function successResponse(string $message = 'Email sent successfully.'): array {
        return [
            'success' => true,
            'message' => $message
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | Failed Response
    |--------------------------------------------------------------------------
    */
    private static function failedResponse(string $message = 'Unable to send email.'): array {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}