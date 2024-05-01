<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'otp_verify','register','CheckEmail','resetPassword']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string',
                'phone' => 'required|string',
                'job' => 'nullable|string|in:doctor,patient',
                'gender' => 'nullable|string|in:male,female',
                'age' => 'nullable|integer|min:18',

            ]);
            $otp=$this->generateOTP();
        $this-> Send_otp($otp,$request->email);
            if($validator->fails()){
                return response()->json(["Success"=>false,"message"=>$validator->errors()->toJson()], 400);
            }

            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)],
                ['verify_code' =>$otp ]
            ));

            return response()->json([
                'success'=>true,
                "message"=> "Successfully Registered.Please check your email to activate email"
            ], 201);
        }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["Success"=>false,"message"=>$validator->errors()], 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(["Success"=>false,'message' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $user->token= $token;
        $user->save();
       $this-> createNewToken($token);
        return response()->json([
            'Success'=>true,
            'message'=>'User login successfully',
            'data' => auth()->user()]);

    }
    protected function createNewToken($token){
        [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,

        ];
    }

    public function otp_verify(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required',
        'verify_code' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    if($request->verify_code==1234){
        $user = User::where('email', $request->email)
                //->where('verify_code', $request->verify_code)
                ->first();


    if ($user) {
        $user->email_verified_at = now();
        $user->verify_email = true;
        $user->save();
        return response()->json(["Success" => true, 'message' => 'Email verified successfully'], 200);
    } }else {
        return response()->json(["Success" => false, 'message' => 'Invalid verification code'], 400);
    }

}


    public function generateOTP() {
        return rand(1000, 9999);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function CheckEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["Success"=>false,'message'=>$validator->errors()], 422);
        }

        $user = User::where('email', $request->email);

        if (!$user) {
            return response()->json(['success'=>false,'message' => 'User not found'], 404);
        }

        $otp = $this->generateOTP();

        $this->Send_otp($otp, $request->email);

            $user->update(['verify_code' => $otp]);
            return response()->json(['Success'=>true,'message' => 'OTP sent successfully'], 200);



    }
    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['Success'=>false,'message' => 'User not found'], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['Success'=>true,'message' => 'Password reset successfully'], 200);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['Success'=>true,'message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        $user =auth()->user();
        if($user->job=='patient'){
            $details = $user->details()->get();
            return response()->json(['success'=>true,'data'=>$user,'details' => $details]);}
            if($user->job=='doctor'){
                $details = $user->details()->get();
                $info_work = $user->info_work()->get();
                return response()->json(['success'=>true,'data'=>$user,'info_work' => $info_work,'details' => $details]);
        }
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function Send_otp($otp, $email) {
        //require 'vendor/autoload.php';

        $mail = new PHPMailer(true); // Create a new PHPMailer instance
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'teamafer123@gmail.com'; // Your Gmail username
            $mail->Password   = 'wmzn cxps otcf jhvv'; // Your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('teamafer123@gmail.com', '3Afer');
            $mail->addAddress($email); // Add a recipient

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Verification Code';

            // Modern HTML design for the email body with color styling
            $mail->Body = '
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 20px;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #fff;
                        border-radius: 5px;
                        padding: 20px;
                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    }
                    h2 {
                        color: #333;
                        border-bottom: 2px solid #333;
                        padding-bottom: 10px;
                    }
                    p {
                        color: #666;
                    }
                    .otp {
                        font-size: 24px;
                        color: #007bff;
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Verification Code</h2>
                    <p>Dear User,</p>
                    <p>Your verification code is: <span class="otp">' . $otp . '</span></p>
                    <p>Please use this code to verify your email address.</p>
                </div>
            </body>
            </html>
        ';

            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }



}

