<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Requests\API\ForgotPasswordEmailRequest;
use App\Http\Requests\API\ForgotPasswordEmailVerifierRequest;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Controllers\AppBaseController;

use App\Myclass\PHPMailer;
use App\Myclass\SMTP;

class ForgotPasswordController extends AppBaseController
{
    public function email(ForgotPasswordEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $rand = rand(10, 10000);
            $base_rend = base64_encode($rand);
            $user->remember_token = $base_rend;
            $user->save();
            // dd($base_rend, $rand);
            $data['user'] = $user;
            $data['code'] = $rand;

            try {

                $message = view('mail.code')->with(['data' => $data]);

                $mail = new PHPMailer();
                $mail->IsSMTP();
                // $mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 465; // or 587
                $mail->IsHTML(true);
                $mail->Username = "sazzadul.bubt@gmail.com";
                $mail->Password = "A420145A";
                $mail->SetFrom("sazzadul.bubt@gmail.com", "Md Sazzadul Islam");
                $mail->Subject = "Confirm code";
                $mail->Body = $message;
                $mail->AddAddress($request->email);

                if (!$mail->Send()) {
                    return $this->sendError('Email not send');
                    // echo "Mailer Error: " . $mail->ErrorInfo;
                    // dd($mail->ErrorInfo);
                } else {
                    return $this->sendResponse($data, 'Email retrieved successfully');
                }

                // return response()->json(['message' => 'Thank you for submit your resume. We will contact you soon.'], 200);
            } catch (\Exception $e) {
                // return $e;
                return $this->sendError('Email not send ! Error');
            }

            return $this->sendResponse($data, 'Email retrieved successfully');
        }
        return $this->sendError('Email not found');
    }
    public function email_verifier(ForgotPasswordEmailVerifierRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $request_code = base64_encode($request->code);
            // dd($request_code, $user->remember_token);
            if ($request_code == $user->remember_token) {
                $user->remember_token = null;
                $user->save();
                return $this->sendResponse($user, 'Code matched successfully');
            }
        }
        return $this->sendError('Code not matched');
    }

    public function change_password(ChangePasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = $request->password;
            $user->save();
            return $this->sendResponse($user, 'Password changed successfully');
        }
        return $this->sendError('Code not matched');
    }
}
