<?php

namespace Za\Support\OTP;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PasswordReset
{
    protected static $userModel;

    protected static $format = 'Your OTP code is - {CODE}';

    public static function configure($userModel, $format = null)
    {
        static::$userModel = $userModel;
        static::$format = $format ? $format : static::$format;
    }

    public static function send($phoneNumber)
    {
        $otpCode = mt_rand(101010, 999999);

        DB::table('otp_password_reset')->insert([
            'phone_number' => $phoneNumber,
            'otp_code' => $otpCode,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send OTP Code SMS
        app('smsPoh')->poh($phoneNumber, str_replace('{CODE}', $otpCode, static::$format));
    }

    public static function reset($phoneNumber, $otpCode, $newPassword)
    {
        // get the latest reset code data
        $reset = DB::table('otp_password_reset')->where([
            'phone_number' => $phoneNumber,
        ])->latest('created_at')->first();

        if (! $reset || $reset->otp_code !== $otpCode) {
            throw ValidationException::withMessages([
                'phone_number' => ['Invalid phone number or OTP code.'],
            ]);
        }

        $user = (new static::$userModel)->where('phone_number', $reset->phone_number)->first();
        $user->password = bcrypt($newPassword);
        $user->save();

        return DB::table('otp_password_reset')->where([
            'phone_number' => $phoneNumber,
            'otp_code' => $otpCode,
        ])->delete();
    }
}
