<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // ── Registration ─────────────────────────────────────────────────────────
    'register_title'     => 'Create Your Account',
    'register_subtitle'  => 'Start your journey to find a halal partner',
    'register_success'   => 'Welcome to HeavenlyMatch! Please complete your biodata.',
    'already_member'     => 'Already have an account?',
    'create_account'     => 'Create Account',
    'terms_accept'       => 'I agree to the :terms and :privacy',
    'terms_link'         => 'Terms of Service',
    'privacy_link'       => 'Privacy Policy',

    // ── Login ─────────────────────────────────────────────────────────────────
    'login_title'        => 'Sign In to Your Account',
    'login_subtitle'     => 'Welcome back',
    'login_button'       => 'Sign In',
    'login_google'       => 'Continue with Google',
    'no_account'         => "Don't have an account?",
    'register_link'      => 'Register now',
    'remember_me'        => 'Remember me',
    'forgot_password'    => 'Forgot Password?',

    // ── Social OAuth (Google / Facebook) ─────────────────────────────────────
    'or_continue_with'            => 'or continue with',
    'login_facebook'              => 'Continue with Facebook',
    'register_google'             => 'Sign up with Google',
    'register_facebook'           => 'Sign up with Facebook',
    'social_login_disabled'       => 'This login option is currently unavailable. Please sign in with email and password.',
    'social_login_failed'         => 'Social login failed. Please try again or sign in with your email and password.',
    'social_email_missing'        => 'Could not retrieve your email from this provider. Please register with email and password.',
    'social_welcome'              => 'Welcome! Please complete your profile to find your match.',
    // Legacy key — kept for backward compatibility
    'google_login_not_configured' => 'Google login is not available at this time. Please sign in with your email and password.',
    'google_welcome'              => 'Welcome! Please complete your profile to find your match.',

    // ── Password reset ────────────────────────────────────────────────────────
    'forgot_title'       => 'Reset Your Password',
    'forgot_subtitle'    => 'Enter your email to receive a reset link',
    'forgot_button'      => 'Send Reset Link',
    'forgot_sent'        => 'Password reset link sent to your email.',
    'reset_title'        => 'Set New Password',
    'reset_button'       => 'Reset Password',
    'reset_success'      => 'Password reset successfully. Please sign in.',

    // ── Email verification ────────────────────────────────────────────────────
    'verify_title'       => 'Verify Your Email',
    'verify_subtitle'    => 'We sent a verification link to :email',
    'verify_check_inbox' => 'Check your inbox and click the verification link to continue.',
    'verify_resend'      => 'Resend Verification Email',
    'verify_resent'      => 'Verification email sent. Please check your inbox.',
    'verify_success'     => 'Email verified successfully.',
    'verify_notice'      => 'Please verify your email address before continuing.',
    'verify_logout'      => 'Sign out and use a different account',

    // ── Platform mode selection ───────────────────────────────────────────────
    'mode_title'            => 'Choose Your Mode',
    'mode_general_title'    => 'General Matrimony',
    'mode_general_desc'     => 'Browse profiles freely. Photos visible to members. Direct communication after mutual interest.',
    'mode_islamic_title'    => 'Islamic / Halal Mode',
    'mode_islamic_desc'     => 'Photos blurred by default. Guardian (Wali) involvement. Privacy-first approach.',

    // ── Account status ────────────────────────────────────────────────────────
    'account_suspended'  => 'Your account has been banned or suspended. Please contact support.',
    'admin_no_access'    => 'You do not have admin access.',

    // ── Logout ────────────────────────────────────────────────────────────────
    'logout_success'     => 'You have been signed out.',

    // ── Register — step labels & form fields ──────────────────────────────────
    'step_account'           => 'Account',
    'step_profile'           => 'Profile',
    'step_mode'              => 'Mode',
    'btn_continue'           => 'Continue',
    'btn_back'               => 'Back',
    'btn_create'             => 'Create Account',
    'field_full_name'        => 'Full Name',
    'field_full_name_ph'     => 'Your full name',
    'field_email'            => 'Email Address',
    'field_email_ph'         => 'you@example.com',
    'field_password'         => 'Password',
    'field_password_ph'      => 'Min 8 characters',
    'field_confirm_password' => 'Confirm Password',
    'field_confirm_ph'       => 'Repeat your password',
    'i_am_a'                 => 'I am a',
    'groom'                  => '👨 Groom',
    'bride'                  => '👩 Bride',
    'profile_for'            => 'Profile is for',
    'for_self'               => 'Myself',
    'for_son'                => 'My Son',
    'for_daughter'           => 'My Daughter',
    'for_brother'            => 'My Brother',
    'for_sister'             => 'My Sister',
    'for_relative'           => 'A Relative',
    'choose_experience'      => 'Choose your experience',
    'mode_islamic_badge'     => 'Most Chosen',

    // ── Mobile number + OTP verification ──────────────────────────────────────
    'field_mobile'           => 'Mobile Number',
    'field_mobile_ph'        => '+880 1XXX-XXXXXX',
    'mobile_required'        => 'Mobile number is required.',
    'btn_send_otp'           => 'Send OTP',
    'btn_verify_otp'         => 'Verify OTP',
    'btn_resend_otp'         => 'Resend OTP',
    'otp_label'              => 'Enter 6-digit OTP',
    'otp_sent_hint'          => 'We sent a 6-digit OTP to your mobile number.',
    'otp_resend_in'          => 'Resend in :seconds s',
    'phone_verified_badge'   => 'Phone verified',
    'change_number'          => 'Change number',
    'verifying'              => 'Verifying…',
    'verify_phone_first'     => 'Please verify your mobile number to continue.',

    // OTP server messages (also used by PhoneOtpService / controllers)
    'otp_sent'               => 'We sent a 6-digit OTP to your mobile number.',
    'otp_invalid_phone'      => 'Please enter a valid Bangladeshi mobile number.',
    'otp_invalid'            => 'Invalid OTP. Please try again.',
    'otp_expired'            => 'Your OTP has expired. Please request a new one.',
    'otp_too_many'           => 'Too many attempts. Please request a new OTP.',
    'otp_verified'           => 'Phone verified.',
    'otp_resend_wait'        => 'Please wait :seconds seconds before requesting another OTP.',
    'otp_send_failed'        => 'OTP could not be sent right now. Please try again later.',
    'otp_no_code'            => 'Please request an OTP first.',
    'otp_phone_taken'        => 'This mobile number is already registered.',
    'otp_not_verified'       => 'Please verify your mobile number before continuing.',

    // ── Trust side panel (Register & Login) ───────────────────────────────────
    'trust_title'            => 'Find Your Halal Match',
    'trust_subtitle'         => 'Join 50,000+ Muslim families on Bangladesh\'s most trusted matrimony platform.',
    'trust_f1'               => '50,000+ Verified Profiles',
    'trust_f2'               => 'Guardian / Wali Support',
    'trust_f3'               => 'Photo Privacy Controls',
    'trust_f4'               => 'SSL Encrypted & Secure',
    'trust_f5'               => 'Bangladesh-First Platform',
    'trust_quote'            => '"Alhamdulillah, we found each other here. JazakAllah khair to HeavenlyMatch."',
    'trust_quote_author'     => 'A Happy Couple, Dhaka',
    'login_trust_title'      => 'Welcome Back',
    'login_trust_subtitle'   => 'Your halal journey continues. May Allah bless your search.',
    'login_privacy_notice'   => 'Your data is encrypted and never shared',

];
